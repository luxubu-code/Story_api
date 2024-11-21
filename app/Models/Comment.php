<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'story_id','content','parent_id','likes'];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function story(){
        return $this->belongsTo(Story::class, 'story_id', 'story_id');
    }
    public function parent(){
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function replies(){
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
