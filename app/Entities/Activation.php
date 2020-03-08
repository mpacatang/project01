<?php 

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    protected $table = 'activations';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}