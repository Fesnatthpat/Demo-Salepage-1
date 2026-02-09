<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Add this line
use Illuminate\Validation\ValidationException;

class HomepageContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homepageContents = HomepageContent::orderBy('section_name')
                                            ->orderBy('order')
                                            ->get()
                                            ->groupBy('section_name');
        return view('admin.homepage-content.index', compact('homepageContents'));
    }

    /**
     * Show the live editable version of the homepage.
     */
    public function liveEdit()
    {
        // Fetch homepage data just like HomeController@index
        $recommendedProducts = ProductSalepage::with('images')
            ->where('pd_sp_active', 1)
            ->where('is_recommended', 1)
            ->orderBy('pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        $homepageContents = HomepageContent::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('section_name');

        return view('admin.homepage-content.live-edit', compact('recommendedProducts', 'homepageContents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.homepage-content.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'section_name' => 'required|string|max:255',
            'item_key' => 'nullable|string|max:255',
            'type' => 'required|string|in:image,text,icon,link,collection',
            'value' => 'nullable|string',
            'data' => 'nullable|json',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (isset($validatedData['data'])) {
            $validatedData['data'] = json_decode($validatedData['data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['data' => 'The data field must be a valid JSON string.']);
            }
        }

        HomepageContent::create($validatedData);

        return redirect()->route('admin.homepage-content.index')->with('success', 'Homepage content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HomepageContent $homepageContent)
    {
        return view('admin.homepage-content.show', compact('homepageContent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomepageContent $homepageContent)
    {
        return view('admin.homepage-content.edit', compact('homepageContent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomepageContent $homepageContent)
    {
        $validatedData = $request->validate([
            'section_name' => 'required|string|max:255',
            'item_key' => 'nullable|string|max:255',
            'type' => 'required|string|in:image,text,icon,link,collection',
            'value' => 'nullable|string',
            'data' => 'nullable|json',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (isset($validatedData['data'])) {
            $validatedData['data'] = json_decode($validatedData['data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['data' => 'The data field must be a valid JSON string.']);
            }
        }

        $homepageContent->update($validatedData);

        return redirect()->route('admin.homepage-content.index')->with('success', 'Homepage content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomepageContent $homepageContent)
    {
        $homepageContent->delete();

        return redirect()->route('admin.homepage-content.index')->with('success', 'Homepage content deleted successfully.');
    }

    /**
     * Update a specific value for a homepage content item (for in-context editing).
     */
    public function updateValue(Request $request, HomepageContent $homepageContent)
    {
        $validatedData = $request->validate([
            'field' => 'required|string', // Can be 'value', 'data', or 'data.nested_field'
            'new_value' => 'nullable',
            'image_file' => 'nullable|image|max:2048', // For image uploads
        ]);

        $field = $validatedData['field'];
        $newValue = $validatedData['new_value'];
        $imageFile = $request->file('image_file');

        // Handle image file upload
        if ($imageFile) {
            $path = $imageFile->store('public/homepage_images'); // Store image in storage/app/public/homepage_images
            $newValue = Storage::url($path); // Get public URL
            // If the field is a nested image within 'data', we need to prepare the data array
            if (str_starts_with($field, 'data.')) {
                $homepageContent->data = $homepageContent->data ?? [];
                data_set($homepageContent->data, substr($field, 5), $newValue);
                $homepageContent->data = json_decode(json_encode($homepageContent->data)); // Ensure it's stored as JSON
            } else {
                $homepageContent->value = $newValue;
            }
            $homepageContent->save();
            return response()->json(['success' => true, 'message' => 'Image updated successfully.', 'new_image_url' => $newValue]);
        }

        // Handle non-file updates (text, icon, link, or nested data)
        if ($field === 'value') {
            $homepageContent->value = $newValue;
        } elseif (str_starts_with($field, 'data.')) {
            // Update nested data field
            $homepageContent->data = $homepageContent->data ?? []; // Initialize if null
            data_set($homepageContent->data, substr($field, 5), $newValue); // Use Laravel's data_set helper
            $homepageContent->data = json_decode(json_encode($homepageContent->data)); // Ensure it's stored as JSON
        } elseif ($field === 'data') {
            // Update entire data object
            $homepageContent->data = json_decode($newValue, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid JSON for data field'], 422);
            }
        } else {
            return response()->json(['error' => 'Invalid field specified for update.'], 400);
        }

        $homepageContent->save();

        return response()->json(['success' => true, 'message' => 'Content updated successfully.']);
    }
}
