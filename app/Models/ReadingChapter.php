<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingChapter extends Model
{
    protected $table = 'read_chapters';
    protected $fillable = ['id', 'user_id', 'chapter_id','story_id', 'created_at','updated_at'];
    public function readinghistory()
    {
        return $this->belongsTo(ReadingHistory::class, 'story_id', 'story_id');
    }
}
