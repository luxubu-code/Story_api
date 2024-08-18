<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_id','updated_at'];
    public function stories(){
        return $this->hasMany(Story::class, 'category_id','category_id');
    }
}