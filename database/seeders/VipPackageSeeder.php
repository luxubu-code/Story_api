<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VipPackage;

class VipPackageSeeder extends Seeder
{
    public function run()
    {
        VipPackage::create([
            'name' => 'VIP Tháng',
            'duration' => 1,
            'price' => 30000,
            'description' => 'Gói VIP 1 tháng',
            'features' => [
                'Đọc truyện VIP',
                'Huy hiệu VIP thường',
                'Tải truyện VIP về máy'
            ]
        ]);

        VipPackage::create([
            'name' => 'VIP Năm',
            'duration' => 12,
            'price' => 300000,
            'description' => 'Gói VIP 1 năm (Tiết kiệm 100,000đ)',
            'features' => [
                'Đọc truyện VIP',
                'Tải truyện về máy',
                'Huy hiệu VIP đặc biệt'
            ]
        ]);
    }
}
