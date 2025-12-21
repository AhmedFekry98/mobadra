<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'base_price',
                'value' => '100.00',
                'caption' => 'Base Price',
                'description' => 'Base price for the service',
            ],
            [
                'key' => 'credit_price_per_unit',
                'value' => '1.00',
                'caption' => 'Credit Price Per Unit',
                'description' => 'Price per unit of credit',
            ],
            [
                'key' => 'app_name',
                'value' => 'CompareThePro',
                'caption' => 'App Name',
                'description' => 'Name of the application',
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'caption' => 'Currency',
                'description' => 'Currency of the application',
            ],
            [
                'key' => 'tax_rate',
                'value' => '10.00',
                'caption' => 'Tax Rate',
                'description' => 'Tax rate for the service',
            ],
            [
                'key' => 'service_fee',
                'value' => '5.00',
                'caption' => 'Service Fee',
                'description' => 'Service fee for the service',
            ]
        ];

        foreach ($settings as $setting) {
            GeneralSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'caption' => $setting['caption'],
                    'description' => $setting['description']
                 ]
            );
        }
    }
}
