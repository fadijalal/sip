<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
       protected $fillable = [
     'student_id',
        'opportunity_id',
        'skills',
        'motivation',
        'status',
        'cv',
    ];

    //      protected $casts = [
    //     'skils'=>'array',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
        public function opportunity()
    {
    return $this->belongsTo(InternshipOpportunity::class, 'opportunity_id');
    }


}
