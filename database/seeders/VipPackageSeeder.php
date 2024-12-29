<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VipPackage;

class VipPackageSeeder extends Seeder
{
    public function run()
    {
        VipPackage::create([
            'name' => 'Gói VIP Tháng',
            'price' => 100000,
            'duration_days' => 30,
            'description' => 'Đọc truyện VIP trong 1 tháng'
        ]);

        VipPackage::create([
            'name' => 'Gói VIP Năm',
            'price' => 1000000,
            'duration_days' => 365,
            'description' => 'Đọc truyện VIP 1 năm với ưu đãi đặc biệt'
        ]);
    }
}