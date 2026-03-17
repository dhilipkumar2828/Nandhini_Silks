<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
        ]);

        if (!auth()->check()) {
            return back()->with('error', 'You must be logged in to save an address.');
        }

        UserAddress::create([
            'user_id' => auth()->id(),
            'label' => $request->label,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country ?? 'India',
            'landmark' => $request->landmark,
        ]);

        return back()->with('success', 'Address saved successfully. It is now available for your orders.');
    }
}
