<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternshipOpportunity extends Model
{
    protected $fillable = [
        'company_user_id',
        'title',
        'description',
        'type',
        'field',
        'city',
        'work_type',
        'requirements',
        'education_level',
        'duration',
        'deadline',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'opportunity_id');
    }
}
