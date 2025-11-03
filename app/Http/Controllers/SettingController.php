<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::grouped();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            foreach ($request->input('settings', []) as $key => $value) {
                // Handle file uploads
                if ($request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    $path = $file->store('settings', 'public');
                    $value = $path;
                }

                $setting = Setting::firstOrNew(['key' => $key]);
                $setting->value = $value;
                
                // Auto-detect type if not set
                if (!$setting->exists) {
                    if (is_bool($value)) {
                        $setting->type = 'boolean';
                    } elseif (is_numeric($value) && strpos($value, '.') !== false) {
                        $setting->type = 'decimal';
                    } elseif (is_numeric($value)) {
                        $setting->type = 'integer';
                    } else {
                        $setting->type = 'string';
                    }
                }
                
                $setting->save();
            }

            return response()->json([
                'ok' => true,
                'message' => 'Settings updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific setting
     */
    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'ok' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $setting,
        ]);
    }

    /**
     * Create a new setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:string,boolean,integer,decimal,json,file',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $setting = Setting::create($request->all());

        return response()->json([
            'ok' => true,
            'message' => 'Setting created successfully',
            'data' => $setting,
        ], 201);
    }

    /**
     * Delete a setting
     */
    public function destroy($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'ok' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        // Delete associated file if exists
        if ($setting->type === 'file' && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Setting deleted successfully',
        ]);
    }
}
