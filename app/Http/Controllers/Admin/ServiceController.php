<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display all services.
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        $services = $query->orderBy('id', 'desc')->paginate(10);

        $categories = ServiceCategory::where('status', 'enabled')->get();

        return view('admin.service.index', compact('services', 'categories'));
    }

    /**
     * Show form for creating a new service.
     */
    public function create()
    {
        $categories = ServiceCategory::where('status', 'enabled')->get();
        return view('admin.service.create', compact('categories'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'metadata' => 'nullable|json',
            'status' => 'required|in:enabled,disabled',
        ]);

        Service::create([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'base_price' => $request->base_price,
            'duration_minutes' => $request->duration_minutes,
            'metadata' => $request->metadata ? json_decode($request->metadata, true) : null,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $categories = ServiceCategory::where('status', 'enabled')->get();

        return view('admin.service.edit', compact('service', 'categories'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'metadata' => 'nullable|json',
            'status' => 'required|in:enabled,disabled',
        ]);

        $service->update([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'base_price' => $request->base_price,
            'duration_minutes' => $request->duration_minutes,
            'metadata' => $request->metadata ? json_decode($request->metadata, true) : null,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service.
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Toggle enable/disable.
     */
    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->status = $service->status === 'enabled' ? 'disabled' : 'enabled';
        $service->save();

        return redirect()->route('admin.services.index')
            ->with('success', 'Status updated successfully.');
    }
}