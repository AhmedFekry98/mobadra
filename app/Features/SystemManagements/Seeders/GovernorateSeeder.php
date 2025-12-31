<?php

namespace App\Features\SystemManagements\Seeders;

use App\Features\SystemManagements\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            ['name' => 'Cairo', 'code' => 'CAI'],
            ['name' => 'Alexandria', 'code' => 'ALX'],
            ['name' => 'Giza', 'code' => 'GIZ'],
            ['name' => 'Qalyubia', 'code' => 'QLY'],
            ['name' => 'Port Said', 'code' => 'PTS'],
            ['name' => 'Suez', 'code' => 'SUZ'],
            ['name' => 'Luxor', 'code' => 'LXR'],
            ['name' => 'Aswan', 'code' => 'ASW'],
            ['name' => 'Asyut', 'code' => 'ASY'],
            ['name' => 'Beheira', 'code' => 'BHR'],
            ['name' => 'Beni Suef', 'code' => 'BNS'],
            ['name' => 'Dakahlia', 'code' => 'DKH'],
            ['name' => 'Damietta', 'code' => 'DMT'],
            ['name' => 'Faiyum', 'code' => 'FYM'],
            ['name' => 'Gharbia', 'code' => 'GHR'],
            ['name' => 'Ismailia', 'code' => 'ISM'],
            ['name' => 'Kafr El Sheikh', 'code' => 'KFS'],
            ['name' => 'Matruh', 'code' => 'MTR'],
            ['name' => 'Minya', 'code' => 'MNY'],
            ['name' => 'Monufia', 'code' => 'MNF'],
            ['name' => 'New Valley', 'code' => 'NVL'],
            ['name' => 'North Sinai', 'code' => 'NSN'],
            ['name' => 'Qena', 'code' => 'QNA'],
            ['name' => 'Red Sea', 'code' => 'RDS'],
            ['name' => 'Sharqia', 'code' => 'SHR'],
            ['name' => 'Sohag', 'code' => 'SHG'],
            ['name' => 'South Sinai', 'code' => 'SSN'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::updateOrCreate(
                ['code' => $governorate['code']],
                [
                    'name' => $governorate['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
