<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    protected function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'address1' => 'required|string|max:255',
            'recipient_phone' => ['required', 'regex:/^\d{10}$/'],
            'city' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'state' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'zip' => ['required', 'regex:/^\d{6}$/'],
            'country' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
        ]);
    }

    public function store(Request $request)
    {
        $this->validateAddress($request);

        if (!auth()->check()) {
            return back()->with('error', 'You must be logged in to save an address.');
        }

        UserAddress::create([
            'user_id' => auth()->id(),
            'label' => $request->label,
            'recipient_phone' => $request->recipient_phone,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
            'landmark' => $request->landmark,
        ]);

        return back()->with('success', 'Address saved successfully. It is now available for your orders.');
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->validateAddress($request);

        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to update an address.');
        }

        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->update([
            'label' => $request->label,
            'recipient_phone' => $request->recipient_phone,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
            'landmark' => $request->landmark,
        ]);

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(UserAddress $address)
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to delete an address.');
        }

        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Address deleted successfully.');
    }
}
