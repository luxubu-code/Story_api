<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VipPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'duration',
        'price',
        'description',
        'features'  // JSON chứa các tính năng của gói
    ];

    protected $casts = [
        'features' => 'array'
    ];
}
