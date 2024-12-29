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

    protected $dates = ['start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(VipPackage::class, 'vip_package_id');
    }
}