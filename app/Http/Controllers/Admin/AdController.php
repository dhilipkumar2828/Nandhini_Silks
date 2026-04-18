<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Ad::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('status', '=', $status);
        }

        $ads = $query->latest()->paginate($perPage)->withQueryString();
        return view('admin.appearance.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.appearance.ads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link' => 'nullable|string|max:255',
            'open_new_tab' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/ads'), $imageName);
            $data['image'] = 'ads/'.$imageName;
        }

        Ad::create($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad created successfully.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.appearance.ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link' => 'nullable|string|max:255',
            'open_new_tab' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($ad->image && file_exists(public_path('uploads/' . $ad->image))) {
                unlink(public_path('uploads/' . $ad->image));
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/ads'), $imageName);
            $data['image'] = 'ads/'.$imageName;
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad updated successfully.');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image && file_exists(public_path('uploads/' . $ad->image))) {
            unlink(public_path('uploads/' . $ad->image));
        }
        $ad->delete();

        return redirect()->route('admin.ads.index')->with('success', 'Ad deleted successfully.');
    }
}
