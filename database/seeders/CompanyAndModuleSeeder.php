<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class CompanyAndModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (CompanySetting::query()->count() === 0) {
            CompanySetting::query()->create(CompanySetting::defaultAttributes());
        }
        CompanySetting::flushCache();

        foreach (SystemModule::defaultModules() as $module) {
            SystemModule::query()->updateOrCreate(
                ['key' => $module['key']],
                $module
            );
        }
        SystemModule::flushCache();
    }
}
