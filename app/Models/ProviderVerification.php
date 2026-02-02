<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderVerification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'skill_types' => 'array',
        'has_compliance_certificates' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
