<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function likeNotification()
    {
        return $this->hasOne(LikeNotification::class, 'like_id');
    }
}