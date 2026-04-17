<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

use App\Mail\InquiryResponseMail;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::query();

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('message', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', '=', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $inquiries = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,responded',
            'admin_note' => 'nullable|string'
        ]);

        $inquiry->update($request->only(['status', 'admin_note']));

        if ($inquiry->status == 'responded') {
            try {
                Mail::to($inquiry->email)->send(new InquiryResponseMail($inquiry));
            } catch (\Exception $e) {
                return redirect()->route('admin.inquiries.index')->with('warning', 'Inquiry updated, but email failed to send. Check logs.');
            }
        }

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry updated successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
