<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    protected $table = 'reading_histories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','user_id', 'story_id', 'read_at','created_at', 'updated_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
    public function readingchapters()
    {
        return $this->hasMany(ReadingChapter::class,'user_id','user_id');
    }
}
