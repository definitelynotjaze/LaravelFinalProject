<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Contact;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $menuItems  = MenuItem::available()->orderBy('category')->orderBy('name')->get();
        $categories = $menuItems->pluck('category')->unique()->values();

        return view('home', compact('menuItems','categories'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        Contact::create($request->only('name','email','subject','message'));

        return back()->with('contact_success', 'Thank you! Your message has been sent. We\'ll get back to you soon.');
    }
}
