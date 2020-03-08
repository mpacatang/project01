<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';
    protected $guarded = ['id'];
    protected $appends = ['photo_url'];

    public function scopeCreatedAtDescending($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getPhotoUrlAttribute()
    {
        return url('media/' . $this->user_id . '/story-photos/' . $this->photo);
    }

    public function storyViewers()
    {
        return $this->hasMany(StoryViewer::class, 'story_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}