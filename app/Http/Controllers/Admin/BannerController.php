<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
            'banners.*.image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
                $imageName = 'banner_desk_'.time().'_'.$index.'.'.$file->extension();
                $file->move(public_path('uploads/banners'), $imageName);
                $data['image'] = 'banners/'.$imageName;
            }

            if ($request->hasFile("banners.$index.image_mobile")) {
                $file = $request->file("banners.$index.image_mobile");
                $imageName = 'banner_mob_'.time().'_'.$index.'.'.$file->extension();
                $file->move(public_path('uploads/banners'), $imageName);
                $data['image_mobile'] = 'banners/'.$imageName;
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
            'image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->except(['image', 'image_mobile']);

        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path('uploads/' . $banner->image))) {
                unlink(public_path('uploads/' . $banner->image));
            }
            $imageName = 'banner_desk_'.time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $data['image'] = 'banners/'.$imageName;
        }

        if ($request->hasFile('image_mobile')) {
            if ($banner->image_mobile && file_exists(public_path('uploads/' . $banner->image_mobile))) {
                unlink(public_path('uploads/' . $banner->image_mobile));
            }
            $imageName = 'banner_mob_'.time().'.'.$request->image_mobile->extension();
            $request->image_mobile->move(public_path('uploads/banners'), $imageName);
            $data['image_mobile'] = 'banners/'.$imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image && file_exists(public_path('uploads/' . $banner->image))) {
            unlink(public_path('uploads/' . $banner->image));
        }
        if ($banner->image_mobile && file_exists(public_path('uploads/' . $banner->image_mobile))) {
            unlink(public_path('uploads/' . $banner->image_mobile));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}

