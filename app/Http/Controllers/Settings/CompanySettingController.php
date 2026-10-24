<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CompanySettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-usuarios|ver-roles|ver-permisos'),
        ];
    }

    public function edit()
    {
        return view('settings.company.edit', [
            'company' => CompanySetting::current(),
            'colors' => CompanySetting::availableColors(),
            'icons' => CompanySetting::availableIcons(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'business_subtitle' => ['required', 'string', 'max:120'],
            'nit' => ['required', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:120'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'invoice_prefix' => ['required', 'string', 'max:15'],
            'purchase_prefix' => ['required', 'string', 'max:15'],
            'primary_color' => ['required', Rule::in(array_keys(CompanySetting::availableColors()))],
            'icon_class' => ['required', Rule::in(array_keys(CompanySetting::availableIcons()))],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
        ]);

        $company = CompanySetting::query()->first();
        if ($company) {
            $company->update($data);
        } else {
            CompanySetting::query()->create($data);
        }

        CompanySetting::flushCache();

        return redirect()->route('settings.company.edit')
            ->with('success', 'Identidad Corporativa y parámetros fiscales actualizados correctamente.');
    }
}
