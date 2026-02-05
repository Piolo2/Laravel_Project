<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'admin_name',
        'description',
        'date_posted',
        'deadline',
    ];

    public function scopeActive($query)
    {
        return $query->where('deadline', '>', now()->toDateString());
    }
}
