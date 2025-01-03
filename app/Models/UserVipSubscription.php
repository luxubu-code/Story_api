<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVipSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'vip_package_id',
        'start_date',
        'end_date',
        'payment_status',
        'vnpay_transaction_id'
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $dates = ['start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(VipPackage::class, 'vip_package_id');
    }
    // Trong model UserVipSubscription
    public function isActive()
    {
        return $this->payment_status === 'completed' &&
            $this->end_date->isFuture();
    }

    public function getDaysRemaining()
    {
        if (!$this->isActive()) {
            return 0;
        }
        return now()->diffInDays($this->end_date);
    }

    public function vipPackage()
    {
        return $this->belongsTo(VipPackage::class);
    }
}