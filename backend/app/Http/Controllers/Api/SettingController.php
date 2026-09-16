<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all settings (Admin only)
     */
    public function index(Request $request)
    {
        $group = $request->input('group');
        
        $query = Setting::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        $settings = $query->get()->groupBy('group');
        
        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Get footer settings (Public)
     */
    public function footer()
    {
        $settings = Setting::getByGroup('footer');
        
        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Bulk update settings (Admin only)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'sometimes|string',
            'settings.*.group' => 'sometimes|string',
        ]);

        foreach ($validated['settings'] as $settingData) {
            Setting::set(
                $settingData['key'],
                $settingData['value'] ?? '',
                $settingData['type'] ?? 'text',
                $settingData['group'] ?? 'general'
            );
        }

        Setting::clearCache();

        return response()->json([
            'message' => 'Settings updated successfully'
        ]);
    }
}
