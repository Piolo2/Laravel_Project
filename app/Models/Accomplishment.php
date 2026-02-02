<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accomplishment extends Model
{
    protected $fillable = ['user_id', 'image_path', 'caption'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
