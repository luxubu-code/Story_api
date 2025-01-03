<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateVipStatus extends Command
{
    protected $signature = 'users:update-vip-status';

    public function handle()
    {
        User::whereHas('vipSubscriptions', function ($query) {
            $query->where('payment_status', 'completed')
                ->where('end_date', '<=', now());
        })->update(['is_vip' => false]);
    }
}