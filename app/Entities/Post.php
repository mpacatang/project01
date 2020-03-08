<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $guarded = ['id'];
    protected $appends = ['photo_url'];

    public function scopeCreatedAtDescending($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getPhotoUrlAttribute()
    {
        return url('media/' . $this->user_id . '/post-photos/' . $this->photo);
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userSavedPosts()
    {
        return $this->belongsToMany(User::class, 'saved_posts');
    }
}