<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class FollowNotification extends Model
{
    protected $table = 'follow_notifications';
    protected $guarded = ['id'];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function follow()
    {
        return $this->belongsTo(Follow::class);
    }
}