<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Story;
use App\Models\User;

class FavoriteStories extends Model
{
    protected $table = 'favorite_stories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','user_id', 'story_id','read_at','created_at'];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function story(){
        return $this->belongsTo(Story::class, 'story_id', 'story_id');
    }
}