<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
class Image extends Model
{
    use MediaAlly;
    protected $table = 'images';
    protected $primaryKey = 'image_id';
    protected $fillable = ['image_id', 'chapter_id', 'image_url',];
    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'chapter_id');
    }
    
}