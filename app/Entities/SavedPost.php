<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    protected $table = 'saved_posts';
    protected $guarded = ['id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}