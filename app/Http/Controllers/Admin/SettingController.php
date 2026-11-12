<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function app(Request $request)
    {
        $setting = Setting::whereIn('slug', [
            'app_name',
            'app_email',
            'app_mobile',
            'company_address',
            'about_company',
            'app_favicon',
            'app_logo'
        ])->pluck('value', 'slug');

        if ($request->isMethod('POST')) {

            $request->validate([
                'app_name' => ['required'],
                'app_email' => ['required'],
                'app_mobile' => ['required'],
                'company_address' => ['required'],
                'about_company' => ['required'],
            ]);

            foreach (
                $request->only([
                    'app_name',
                    'app_email',
                    'app_mobile',
                    'company_address',
                    'about_company',
                    'app_favicon',
                    'app_logo'
                ]) as $slug => $value
            ) {

                if (in_array($slug, ['app_favicon', 'app_logo'])) {

                    if (isset($setting[$slug])) {

                        deleteImage($setting[$slug]);
                    }

                    $value = uploadFile($value, 'setting', $slug);
                }

                Setting::updateOrCreate([
                    'slug' => $slug
                ], [
                    'name' => ucfirst(str_replace('_', ' ', $slug)),
                    'value' => $value
                ]);
            }

            return redirect()
                ->route('setting.app')
                ->with('res.success', 'App info updated successful.');
        }

        return view('admin.setting.app', compact('setting'));
    }

    public function bank(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'bank_name' => ['required'],
                'account_no' => ['required'],
                'iban' => ['required'],
                'bank_holder_name' => ['required'],
            ]);

            foreach ($request->only(['bank_name', 'account_no', 'iban', 'bank_holder_name']) as $slug => $value) {

                Setting::updateOrCreate([
                    'slug' => $slug
                ], [
                    'name' => ucfirst(str_replace('_', ' ', $slug)),
                    'value' => $value
                ]);
            }

            return redirect()
                ->route('setting.bank')
                ->with('res.success', 'Bank info updated successful.');
        }

        $setting = Setting::whereIn('slug', [
            'bank_name',
            'account_no',
            'iban',
            'bank_holder_name'
        ])->pluck('value', 'slug');

        return view('admin.setting.bank', compact('setting'));
    }
}
