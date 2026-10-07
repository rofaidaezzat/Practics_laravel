<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Enumerable
     */
    public function collection(): Enumerable
    {
        return Student::with('courses')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Age',
            'Enrolled Courses',
            'Created At',
        ];
    }

    /**
     * @param Student $student
     * @return array
     */
    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->email,
            $student->phone ?? 'N/A',
            $student->age ?? 'N/A',
            $student->courses->pluck('name')->implode(', ') ?: 'None',
            $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}