<?php

namespace App\Entities;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\NewUserActivation;
use App\Notifications\UserForgotPassword;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        if (empty($this->photo)) {
            return url('media-example/profile-placeholder.png');
        } else {
            return url('media/' . $this->id . '/profile-photo/' . $this->photo);
        }
    }

    public function findForPassport($email)
    {
        return $this->where('email', $email)
                    ->orWhere('username', $email)
                    ->first();
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function feed()
    {
        return $this->hasManyThrough(Post::class, Follow::class, 'follower_id', 'user_id', 'id', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'user_id');
    }

    public function activations()
    {
        return $this->hasMany(Activation::class, 'user_id');
    }

    public function passwordResets()
    {
        return $this->hasMany(Passwordreset::class, 'user_id');
    }

    public function messagesAsSender()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesAsRecipient()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function messageDetails()
    {
        return $this->hasMany(MessageDetails::class, 'sender_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function storyViewers()
    {
        return $this->hasMany(StoryViewer::class, 'user_id');
    }

    public function savedPosts()
    {
        return $this->belongsToMany(Post::class, 'saved_posts');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function sendNewUserActivation($activationCode)
    {
        $this->notify(new NewUserActivation($activationCode));
    }

    public function sendUserForgotPassword($resetCode)
    {
        $this->notify(new UserForgotPassword($resetCode));
    }
}
