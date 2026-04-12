<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = User::query();

        if (Schema::hasTable('orders')) {
            $query->withCount('orders')
                  ->withSum('orders', 'grand_total');
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('account_status', '=', $request->status);
        }

        $users = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['addresses', 'orders']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('addresses');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'account_status' => 'required|in:Active,Inactive',
            'role' => 'required|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.regex' => 'The Full Name field should only contain alphabets and spaces.',
            'phone.regex' => 'The Phone number must be exactly 10 digits.',
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone',
            'dob',
            'gender',
            'account_status',
            'role',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->input('password'); 
        }

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();
            $uploadPath = public_path('uploads/users');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $image->move($uploadPath, $imageName);

            if ($user->profile_picture && file_exists(public_path('uploads/' . $user->profile_picture))) {
                unlink(public_path('uploads/' . $user->profile_picture));
            }

            $data['profile_picture'] = 'users/' . $imageName;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function storeAddress(Request $request, User $user)
    {
        $this->validateAddress($request);

        $isDefault = $request->boolean('is_default');
        if ($isDefault || $user->addresses()->count() === 0) {
            $isDefault = true;
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
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
            'is_default' => $isDefault,
        ]);

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Address added.');
    }

    public function updateAddress(Request $request, User $user, UserAddress $address)
    {
        if ($address->user_id !== $user->id) {
            return redirect()->route('admin.users.edit', $user->id)->with('error', 'Address not found.');
        }

        $this->validateAddress($request);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
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
            'is_default' => $isDefault,
        ]);

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Address updated.');
    }

    private function validateAddress(Request $request)
    {
        return $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'nullable|string|regex:/^[0-9]{6}$/',
            'country' => 'nullable|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ], [
            'recipient_phone.regex' => 'The Recipient Phone number must be exactly 10 digits.',
            'zip.regex' => 'The ZIP code must be exactly 6 digits.',
        ]);
    }

    public function destroyAddress(User $user, UserAddress $address)
    {
        if ($address->user_id !== $user->id) {
            return redirect()->route('admin.users.edit', $user->id)->with('error', 'Address not found.');
        }

        $address->delete();

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Address removed.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
