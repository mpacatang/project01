<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class MessageDetail extends Model
{
    protected $table = 'message_details';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}