<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    protected $fillable = [
        'name',
        'email',
        'phone',
        'age',
    ];

    /**
     * Validation rules (replaces DB-level unique constraint on email).
     * Use these rules in your FormRequest or controller.
     */
    public static array $rules = [
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:students,email'],
        'phone' => ['nullable', 'string', 'max:20'],
        'age'   => ['nullable', 'integer', 'min:1'],
    ];

    /**
     * Update rules (ignore own record for unique check).
     * Usage: Student::updateRules($id)
     */
    public static function updateRules(int $id): array
    {
        $rules = self::$rules;
        $rules['email'] = ['required', 'email', "unique:students,email,{$id}"];
        return $rules;
    }

    /**
     * The courses that belong to the student.
     * (Foreign key: course_student.student_id → students.id)
     */
    public function courses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }
}

