<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentNotification()
    {
        return $this->hasOne(CommentNotification::class, 'notification_id');
    }

    public function followNotification()
    {
        return $this->hasOne(FollowNotification::class, 'notification_id');
    }

    public function likeNotification()
    {
        return $this->hasOne(LikeNotification::class, 'notification_id');
    }
}