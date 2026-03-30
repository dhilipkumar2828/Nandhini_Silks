<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAddressController extends Controller
{
    protected function validateAddress(Request $request): array
    {
        Log::info('ADDRESS VALIDATION START:', $request->all());
        return $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => ['required', 'regex:/^\d{10}$/'],
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'regex:/^\d{6}$/'],
            'country' => ['required', 'string', 'max:255'],
            'landmark' => 'nullable|string|max:255',
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
            'label' => $request->input('label'),
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'landmark' => $request->input('landmark'),
        ]);

        Log::info('ADDRESS STORED SUCCESSFULLY', ['user_id' => auth()->id()]);
        return back()->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->validateAddress($request);

        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to update an address.');
        }

        if ($address->user_id != Auth::id()) {
            abort(403);
        }

        $address->update([
            'label' => $request->input('label'),
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'landmark' => $request->input('landmark'),
        ]);

        Log::info('ADDRESS UPDATED SUCCESSFULLY', ['address_id' => $address->id, 'user_id' => Auth::id()]);
        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(UserAddress $address)
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to delete an address.');
        }

        if ($address->user_id != Auth::id()) {
            abort(403);
        }

        $address->delete();

        Log::info('ADDRESS DELETED SUCCESSFULLY', ['address_id' => $address->id, 'user_id' => Auth::id()]);
        return back()->with('success', 'Address deleted successfully.');
    }
}
