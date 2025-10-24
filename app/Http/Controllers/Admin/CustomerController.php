<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'mobile_number' => 'required|unique:customers,mobile_number',
            'profile_pic' => 'nullable|image|max:2048',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $data = $request->all();

        // Upload profile picture
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);
            $data['profile_pic'] = 'uploads/customers/' . $filename;
        }

        Customer::create($data);

        return redirect()->route('admin.customer.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Show the customer data for edit modal.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    /**
     * Update customer.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'mobile_number' => 'required|unique:customers,mobile_number,' . $customer->id,
            'profile_pic' => 'nullable|image|max:2048',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $data = $request->all();

        // Upload profile picture if provided and delete old one
        if ($request->hasFile('profile_pic')) {
            // Delete old photo if exists
            if ($customer->profile_pic && file_exists(public_path($customer->profile_pic))) {
                unlink(public_path($customer->profile_pic));
            }

            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);
            $data['profile_pic'] = 'uploads/customers/' . $filename;
        }

        $customer->update($data);

        return redirect()->route('admin.customer.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete customer.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->profile_pic && file_exists(public_path($customer->profile_pic))) {
            unlink(public_path($customer->profile_pic));
        }

        $customer->delete();

        return redirect()->route('admin.customer.index')->with('success', 'Customer deleted successfully.');
    }
}