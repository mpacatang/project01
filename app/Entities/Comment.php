<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function commentNotification()
    {
        return $this->hasOne(CommentNotification::class, 'comment_id');
    }
}