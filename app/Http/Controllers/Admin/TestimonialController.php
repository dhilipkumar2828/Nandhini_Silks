<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $testimonials = Testimonial::latest()->paginate($perPage)->withQueryString();
        return view('admin.appearance.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.appearance.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_homepage' => 'required|boolean',
            'status' => 'required|boolean',
            'submitted_at' => 'nullable|date',
        ]);

        $data = $request->except('photo');
        $data['submitted_at'] = $request->submitted_at ?? now();

        if ($request->hasFile('photo')) {
            $imageName = time().'.'.$request->photo->extension();
            $request->photo->move(public_path('uploads/testimonials'), $imageName);
            $data['photo'] = 'testimonials/'.$imageName;
        }

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.appearance.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_homepage' => 'required|boolean',
            'status' => 'required|boolean',
            'submitted_at' => 'nullable|date',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($testimonial->photo && file_exists(public_path('uploads/' . $testimonial->photo))) {
                unlink(public_path('uploads/' . $testimonial->photo));
            }
            $imageName = time().'.'.$request->photo->extension();
            $request->photo->move(public_path('uploads/testimonials'), $imageName);
            $data['photo'] = 'testimonials/'.$imageName;
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo && file_exists(public_path('uploads/' . $testimonial->photo))) {
            unlink(public_path('uploads/' . $testimonial->photo));
        }
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }
}
