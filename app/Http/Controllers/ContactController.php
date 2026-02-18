<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return view('contact', compact('contacts'));
    }
}
