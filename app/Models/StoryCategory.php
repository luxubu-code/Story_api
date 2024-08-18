<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryCategory extends Model
{
    use HasFactory;

    protected $table = 'storycategory';

    protected $fillable = ['story_id', 'category_id'];
}
