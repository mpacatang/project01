<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';
    protected $guarded = ['id'];

    public function followerUser()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followingUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function followNotification()
    {
        return $this->hasOne(FollowNotification::class, 'follow_id');
    }
}