<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
    ];

    /**
     * Validation rules (replaces DB-level unique constraint on code).
     * Use these rules in your FormRequest or controller.
     */
    public static array $rules = [
        'name'        => ['required', 'string', 'max:255'],
        'code'        => ['required', 'string', 'max:50', 'unique:courses,code'],
        'description' => ['nullable', 'string'],
        'credits'     => ['nullable', 'integer', 'min:1'],
    ];

    /**
     * Update rules (ignore own record for unique check).
     * Usage: Course::updateRules($id)
     */
    public static function updateRules(int $id): array
    {
        $rules = self::$rules;
        $rules['code'] = ['required', 'string', 'max:50', "unique:courses,code,{$id}"];
        return $rules;
    }

    /**
     * The students that belong to the course.
     * (Foreign key: course_student.course_id → courses.id)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->withTimestamps();
    }
}
