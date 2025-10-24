<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Technician;

class TechnicianController extends Controller
{
    /**
     * Display a listing of technicians
     */
    public function index(Request $request)
    {
        $query = Technician::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $technicians = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.technician.index', compact('technicians'));
    }

    /**
     * Store a new technician
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:technicians,email',
            'mobile_number' => 'required|unique:technicians,mobile_number',
            'job_role'   => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'availability_status' => 'required|in:available,busy,inactive',
            'status'     => 'required|in:enabled,disabled',
        ]);

        $data = $request->all();

        // Upload profile photo if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/technicians'), $filename);
            $data['profile_photo'] = 'uploads/technicians/' . $filename;
        }

        Technician::create($data);

        return redirect()->route('admin.technician.index')
            ->with('success', 'Technician added successfully.');
    }

    /**
     * Show technician data (for modal edit)
     */
    public function edit($id)
    {
        $technician = Technician::findOrFail($id);
        return response()->json($technician);
    }

    /**
     * Update technician
     */
    public function update(Request $request, $id)
    {
        $technician = Technician::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:technicians,email,' . $technician->id,
            'mobile_number' => 'required|unique:technicians,mobile_number,' . $technician->id,
            'job_role'   => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'availability_status' => 'required|in:available,busy,inactive',
            'status'     => 'required|in:enabled,disabled',
        ]);

        $data = $request->all();

        // Upload profile photo if provided and delete old one
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($technician->profile_photo && file_exists(public_path($technician->profile_photo))) {
                unlink(public_path($technician->profile_photo));
            }

            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/technicians'), $filename);
            $data['profile_photo'] = 'uploads/technicians/' . $filename;
        }

        $technician->update($data);

        return redirect()->route('admin.technician.index')
            ->with('success', 'Technician updated successfully.');
    }

    /**
     * Delete technician
     */
    public function destroy($id)
    {
        $technician = Technician::findOrFail($id);

        // Delete profile photo if exists
        if ($technician->profile_photo && file_exists(public_path($technician->profile_photo))) {
            unlink(public_path($technician->profile_photo));
        }

        $technician->delete();

        return redirect()->route('admin.technician.index')
            ->with('success', 'Technician deleted successfully.');
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $technician = Technician::findOrFail($id);
        $technician->status = $technician->status === 'enabled' ? 'disabled' : 'enabled';
        $technician->save();

        return redirect()->route('admin.technician.index')
            ->with('success', 'Technician status updated.');
    }
}