<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chapter;

class Story extends Model
{
    protected $table = 'stories';
    protected $primaryKey = 'story_id';
    protected $fillable = ['story_id', 'title', 'author', 'description',  'file_name', 'base_url', 'views', 'status', 'created_at', 'updated_at'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'story_id', 'story_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'storycategory', 'story_id', 'category_id');
    }
    public function ratings()
    {
        return $this->hasMany(Ratings::class, 'story_id', 'story_id');
    }
    public function favorites()
    {
        return $this->hasMany(FavoriteStories::class, 'story_id', 'story_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'story_id', 'story_id');
    }
    public function readingHistory()
    {
        return $this->hasMany(ReadingHistory::class, 'story_id', 'story_id');
    }
}