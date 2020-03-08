<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class StoryViewer extends Model
{
    protected $table = 'story_viewers';
    protected $guarded = ['id'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}