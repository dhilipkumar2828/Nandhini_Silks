<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $banners = Banner::orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.appearance.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.appearance.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banners.*.title' => 'nullable|string|max:255',
            'banners.*.link' => 'nullable|string|max:255',
            'banners.*.display_order' => 'required|integer',
            'banners.*.status' => 'required|boolean',
        ]);

        foreach ($request->banners as $index => $bannerData) {
            $data = [
                'title' => $bannerData['title'],
                'link' => $bannerData['link'],
                'display_order' => $bannerData['display_order'],
                'status' => $bannerData['status'],
            ];

            if ($request->hasFile("banners.$index.image")) {
                $file = $request->file("banners.$index.image");
                $imageName = 'banner_'.time().'_'.$index.'.'.$file->extension();
                $file->move(public_path('uploads/banners'), $imageName);
                $data['image'] = 'banners/'.$imageName;
            }

            Banner::create($data);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banners created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.appearance.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->except(['image']);

        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path('uploads/' . $banner->image))) {
                unlink(public_path('uploads/' . $banner->image));
            }
            $imageName = 'banner_'.time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $data['image'] = 'banners/'.$imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image && file_exists(public_path('uploads/' . $banner->image))) {
            unlink(public_path('uploads/' . $banner->image));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
