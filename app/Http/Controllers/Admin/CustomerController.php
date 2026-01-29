<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Assuming 'User' model represents customers
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->guard('admin')->user()->role !== 'superadmin') {
                return redirect()->route('admin.products.index')->with('info', 'You do not have permission to access this page.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm);
            });
        }

        $customers = $query->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $customer)
    {
        $customer->load(['orders' => function ($query) {
            $query->orderBy('ord_date', 'desc');
        }]); // Eager load orders and sort by latest date

        return view('admin.customers.show', compact('customer'));
    }

    // You might add methods for create, store, edit, update, destroy if full CRUD is needed.
}
