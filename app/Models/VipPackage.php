<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VipPackage extends Model
{
    protected $fillable = ['name', 'price', 'duration_days', 'description'];

    public function subscriptions()
    {
        return $this->hasMany(UserVipSubscription::class);
    }
}