<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    protected $table = 'ratings';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'story_id', 'rating', 'created_at', 'updated_at'];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function story(){
        return $this->belongsTo(Story::class, 'story_id', 'story_id');
    }
}
