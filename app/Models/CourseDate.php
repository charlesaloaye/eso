<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDate extends Model
{
    protected $fillable = [
        'course_id',
        'date',
        'start_time',
        'end_time',
        'location'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
