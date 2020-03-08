<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class LikeNotification extends Model
{
    protected $table = 'like_notifications';
    protected $guarded = ['id'];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function like()
    {
        return $this->belongsTo(Like::class);
    }
}