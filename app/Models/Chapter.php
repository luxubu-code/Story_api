<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Story;

class Chapter extends Model
{
    protected $table = 'chapters';
    protected $primaryKey = 'chapter_id';
    protected $fillable = ['chapter_id', 'title', 'story_id', 'views', 'is_vip', 'vip_expiration', 'created_at'];
    protected $casts = ['is_vip' => 'boolean'];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'story_id');
    }
    public function images()
    {
        return $this->hasMany(Image::class, 'chapter_id', 'chapter_id');
    }
    public function history()
    {
        return $this->hasMany(ReadingHistory::class, 'chapter_id', 'chapter_id');
    }
}