<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class AppSettingController extends Controller
{
    /**
     * Show the app settings form
     */
    public function index()
    {
        $settings = AppSetting::first() ?? new AppSetting();
        return view('admin.app_settings.index', compact('settings'));
    }

    /**
     * Store or update app settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'meta_name' => 'nullable|string|max:255',
            'meta_tag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'splash_screen_image' => 'nullable|image|max:2048',
            'splash_screen_title' => 'nullable|string|max:255',
            'splash_screen_description' => 'nullable|string',
            'copyright_message' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url',
            'home_banner' => 'nullable|array',
            'home_banner.*.image' => 'nullable|image|max:2048',
            'home_banner.*.title' => 'nullable|string|max:255',
            'home_banner.*.subtitle' => 'nullable|string|max:255',
        ]);

        $settings = AppSetting::first() ?? new AppSetting();

        // Basic fields
        $settings->site_name = $request->site_name;
        $settings->meta_name = $request->meta_name;
        $settings->meta_tag = $request->meta_tag;
        $settings->description = $request->description;
        $settings->splash_screen_title = $request->splash_screen_title;
        $settings->splash_screen_description = $request->splash_screen_description;
        $settings->copyright_message = $request->copyright_message;

        // Social links (JSON)
        $settings->social_links = $request->social_links;

        // Logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            $settings->logo = 'uploads/settings/' . $filename;
        }

        // Splash screen image
        if ($request->hasFile('splash_screen_image')) {
            $file = $request->file('splash_screen_image');
            $filename = 'splash_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            $settings->splash_screen_image = 'uploads/settings/' . $filename;
        }

        // Home banners
        if ($request->has('home_banner')) {
            $banners = [];
            foreach ($request->home_banner as $index => $banner) {
                $bannerData = [
                    'id' => $banner['id'] ?? $index+1,
                    'title' => $banner['title'] ?? null,
                    'subtitle' => $banner['subtitle'] ?? null,
                ];

                if (isset($banner['image']) && is_object($banner['image'])) {
                    $file = $banner['image'];
                    $filename = 'banner_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/settings'), $filename);
                    $bannerData['image'] = 'uploads/settings/' . $filename;
                } elseif (isset($banner['image_old'])) {
                    $bannerData['image'] = $banner['image_old']; // retain old image if not replaced
                }

                $banners[] = $bannerData;
            }
            $settings->home_banner = $banners;
        }

        $settings->save();

        return redirect()->back()->with('success', 'App settings updated successfully.');
    }
}