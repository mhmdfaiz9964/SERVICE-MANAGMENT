<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * Display all categories.
     */
    public function index(Request $request)
    {
        $query = ServiceCategory::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $categories = $query->orderBy('id', 'desc')->paginate(10);

        $parents = ServiceCategory::where('status', 'enabled')->get();

        return view('admin.service.categories.index', compact('categories', 'parents'));
    }

    /**
     * Show form for creating a new category.
     */
    public function create()
    {
        $parents = ServiceCategory::where('status', 'enabled')->get();
        return view('admin.service.categories.create', compact('parents'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:service_categories,id',
            'status' => 'required|in:enabled,disabled',
        ]);

        $data = [
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $data['icon'] = $path;
        }

        ServiceCategory::create($data);

        return redirect()->route('admin.service.categories.index')
            ->with('success', 'Service Category created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $parents = ServiceCategory::where('id', '!=', $id)->get();

        return view('admin.service.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:service_categories,id',
            'status' => 'required|in:enabled,disabled',
        ]);

        $data = [
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $path = $request->file('icon')->store('icons', 'public');
            $data['icon'] = $path;
        }

        $category->update($data);

        return redirect()->route('admin.service.categories.index')
            ->with('success', 'Service Category updated successfully.');
    }

    /**
     * Delete category.
     */
    public function destroy($id)
    {
        $category = ServiceCategory::findOrFail($id);
        
        // Delete icon if exists
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        
        $category->delete();

        return redirect()->route('admin.service.categories.index')
            ->with('success', 'Service Category deleted successfully.');
    }

    /**
     * Toggle enable/disable.
     */
    public function toggleStatus($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->status = $category->status === 'enabled' ? 'disabled' : 'enabled';
        $category->save();

        return redirect()->route('admin.service.categories.index')
            ->with('success', 'Status updated successfully.');
    }
}