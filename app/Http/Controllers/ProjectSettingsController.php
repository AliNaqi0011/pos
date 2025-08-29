<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserSetting;
use App\Services\FBRService;

class ProjectSettingsController extends Controller
{
    public function index()
    {
        $userSetting = UserSetting::where('user_id', auth()->id())->first();
        
        $settings = [
            'app_name' => $userSetting->app_name ?? 'Laravel Boilerplate',
            'app_logo' => $userSetting->app_logo ?? '/template/images/logo.svg',
            'app_favicon' => $userSetting->app_favicon ?? '/template/images/favicon.png',
            'company_name' => $userSetting->company_name ?? 'Your Company',
            'company_address' => $userSetting->company_address ?? '',
            'company_phone' => $userSetting->company_phone ?? '',
            'company_email' => $userSetting->company_email ?? '',
            'company_website' => $userSetting->company_website ?? '',
            'tax_number' => $userSetting->tax_number ?? '',
            'currency' => $userSetting->currency ?? 'USD',
            'timezone' => $userSetting->timezone ?? 'UTC',
            'date_format' => $userSetting->date_format ?? 'Y-m-d',
            'time_format' => $userSetting->time_format ?? 'H:i:s',
            'fbr_enabled' => $userSetting->fbr_enabled ?? false,
            'fbr_pos_id' => $userSetting->fbr_pos_id ?? '',
            'fbr_username' => $userSetting->fbr_username ?? '',
            'fbr_password' => $userSetting->fbr_password ?? '',
            'fbr_api_url' => $userSetting->fbr_api_url ?? 'https://esp.fbr.gov.pk',
        ];
        
        return view('admin.project-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email',
            'company_website' => 'nullable|url',
            'tax_number' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'fbr_enabled' => 'boolean',
            'fbr_pos_id' => 'nullable|string|max:50',
            'fbr_username' => 'nullable|string|max:100',
            'fbr_password' => 'nullable|string|max:100',
            'fbr_api_url' => 'nullable|url',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        $userSetting = UserSetting::firstOrCreate(['user_id' => auth()->id()]);
        
        $data = [
            'app_name' => $request->app_name,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'company_website' => $request->company_website,
            'tax_number' => $request->tax_number,
            'currency' => $request->currency,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'fbr_enabled' => $request->has('fbr_enabled'),
            'fbr_pos_id' => $request->fbr_pos_id,
            'fbr_username' => $request->fbr_username,
            'fbr_password' => $request->fbr_password,
            'fbr_api_url' => $request->fbr_api_url,
        ];

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('public/uploads');
            $data['app_logo'] = Storage::url($logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $faviconPath = $request->file('app_favicon')->store('public/uploads');
            $data['app_favicon'] = Storage::url($faviconPath);
        }

        $userSetting->update($data);

        return redirect()->back()->with('success', 'Project settings updated successfully!');
    }

    public function testFBRConnection()
    {
        $fbrService = new FBRService();
        $result = $fbrService->testConnection();
        
        return response()->json($result);
    }
}