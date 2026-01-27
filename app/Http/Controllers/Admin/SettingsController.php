<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $data = [
            'business_name' => Setting::get('business_name', 'MaquiVeloso'),
            'phone'         => Setting::get('phone', ''),
            'email'         => Setting::get('email', ''),
            'location'      => Setting::get('location', ''),
        ];

        return view('admin.settings', $data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'location'      => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Definições guardadas com sucesso.');
    }
}
