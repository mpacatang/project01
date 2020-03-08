<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = ['id'];

    public function userSender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function userRecipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function messageDetails()
    {
        return $this->hasMany(MessageDetail::class, 'message_id');
    }

    public function lastMessageDetails()
    {
        return $this->hasOne(MessageDetail::class, 'message_id')->latest('id');
    }
}