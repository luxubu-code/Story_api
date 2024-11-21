<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    protected $table = 'reading_histories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','user_id', 'story_id','chapter_id','base_url','file_name', 'read_at','created_at', 'updated_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function story()
    {
        return $this->belongsTo(Story::class,'story_id','story_id');
    }
    public function chapters()
    {
        return $this->belongsTo(Chapter::class,'chapter_id','chapter_id');
    }
}
