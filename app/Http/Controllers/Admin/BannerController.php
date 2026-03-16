<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('display_order', 'asc')->get();
        return view('admin.appearance.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.appearance.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_desktop' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->except(['image_desktop', 'image_mobile']);

        if ($request->hasFile('image_desktop')) {
            $imageName = 'desktop_'.time().'.'.$request->image_desktop->extension();
            $request->image_desktop->move(public_path('uploads/banners'), $imageName);
            $data['image_desktop'] = 'banners/'.$imageName;
        }

        if ($request->hasFile('image_mobile')) {
            $imageName = 'mobile_'.time().'.'.$request->image_mobile->extension();
            $request->image_mobile->move(public_path('uploads/banners'), $imageName);
            $data['image_mobile'] = 'banners/'.$imageName;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.appearance.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image_desktop' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->except(['image_desktop', 'image_mobile']);

        if ($request->hasFile('image_desktop')) {
            if ($banner->image_desktop && file_exists(public_path('uploads/' . $banner->image_desktop))) {
                unlink(public_path('uploads/' . $banner->image_desktop));
            }
            $imageName = 'desktop_'.time().'.'.$request->image_desktop->extension();
            $request->image_desktop->move(public_path('uploads/banners'), $imageName);
            $data['image_desktop'] = 'banners/'.$imageName;
        }

        if ($request->hasFile('image_mobile')) {
            if ($banner->image_mobile && file_exists(public_path('uploads/' . $banner->image_mobile))) {
                unlink(public_path('uploads/' . $banner->image_mobile));
            }
            $imageName = 'mobile_'.time().'.'.$request->image_mobile->extension();
            $request->image_mobile->move(public_path('uploads/banners'), $imageName);
            $data['image_mobile'] = 'banners/'.$imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_desktop && file_exists(public_path('uploads/' . $banner->image_desktop))) {
            unlink(public_path('uploads/' . $banner->image_desktop));
        }
        if ($banner->image_mobile && file_exists(public_path('uploads/' . $banner->image_mobile))) {
            unlink(public_path('uploads/' . $banner->image_mobile));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
