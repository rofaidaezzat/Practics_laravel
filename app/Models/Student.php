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
     * The courses that belong to the student.
     */
    public function courses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }
}

