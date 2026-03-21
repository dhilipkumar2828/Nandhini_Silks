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

        $users = $query->orderBy('id', 'desc')->paginate(10);

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'account_status' => 'required|in:active,blocked,unverified',
            'role' => 'required|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
        $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create($request->all());

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Address added.');
    }

    public function destroyAddress(User $user, UserAddress $address)
    {
        if ($address->user_id !== $user->id) {
            return redirect()->route('admin.users.edit', $user->id)->with('error', 'Address not found.');
        }

        $address->delete();

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Address removed.');
    }
}
