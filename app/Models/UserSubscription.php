<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'vip_package_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(VipPackage::class, 'vip_package_id');
    }
}
