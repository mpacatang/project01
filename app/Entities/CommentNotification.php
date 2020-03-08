<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CommentNotification extends Model
{
    protected $table = 'comment_notifications';
    protected $guarded = ['id'];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}