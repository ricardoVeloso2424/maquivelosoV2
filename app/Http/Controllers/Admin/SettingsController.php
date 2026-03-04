<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $data = Setting::getSiteSettings([
            'business_name' => 'MaquiVeloso',
            'phone' => '',
            'email' => '',
            'location' => '',
            'contact_phone' => '',
            'contact_email' => '',
            'contact_address' => '',
            'contact_whatsapp' => '',
            'contact_hours' => '',
        ]);

        $data['contact_phone'] = $data['contact_phone'] !== '' ? $data['contact_phone'] : $data['phone'];
        $data['contact_email'] = $data['contact_email'] !== '' ? $data['contact_email'] : $data['email'];
        $data['contact_address'] = $data['contact_address'] !== '' ? $data['contact_address'] : $data['location'];

        return view('admin.settings', $data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'contact_hours' => ['nullable', 'string', 'max:255'],
        ]);

        $legacySettings = [
            'phone' => $validated['contact_phone'] ?? null,
            'email' => $validated['contact_email'] ?? null,
            'location' => $validated['contact_address'] ?? null,
        ];

        foreach (array_merge($validated, $legacySettings) as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Definições guardadas com sucesso.');
    }
}
