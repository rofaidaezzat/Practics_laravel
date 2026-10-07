<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Map each row from the Excel/CSV file to a Student model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row): Model|array|null
    {
        return new Student([
            'name'  => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
            'age'   => isset($row['age']) && $row['age'] !== '' ? (int) $row['age'] : null,
        ]);
    }

    /**
     * Validation rules for each row in the imported file.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'age'   => ['nullable', 'integer', 'min:1', 'max:120'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required'  => 'Row :attribute: Name is required.',
            'email.required' => 'Row :attribute: Email is required.',
            'email.email'    => 'Row :attribute: Must be a valid email address.',
            'email.unique'   => 'Row :attribute: Email :input has already been registered.',
            'age.integer'    => 'Row :attribute: Age must be an integer.',
            'age.min'        => 'Row :attribute: Age must be at least 1.',
            'age.max'        => 'Row :attribute: Age cannot exceed 120.',
        ];
    }
}
