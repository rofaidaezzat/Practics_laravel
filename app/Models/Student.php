<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'age',
    ];

    /**
     * The courses that belong to the student.
     * (Foreign key: course_student.student_id → students.id)
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }
}
