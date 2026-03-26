<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfferCollectionController extends Controller
{
    public function index()
    {
        $offerCollections = OfferCollection::latest()->paginate(10);
        return view('admin.offer-collections.index', compact('offerCollections'));
    }

    public function create()
    {
        return view('admin.offer-collections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        OfferCollection::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status
        ]);

        return redirect()->route('admin.offer-collections.index')->with('success', 'Offer Collection created successfully.');
    }

    public function edit(OfferCollection $offerCollection)
    {
        return view('admin.offer-collections.edit', compact('offerCollection'));
    }

    public function update(Request $request, OfferCollection $offerCollection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $offerCollection->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status
        ]);

        return redirect()->route('admin.offer-collections.index')->with('success', 'Offer Collection updated successfully.');
    }

    public function destroy(OfferCollection $offerCollection)
    {
        $offerCollection->delete();
        return redirect()->route('admin.offer-collections.index')->with('success', 'Offer Collection deleted successfully.');
    }
}
