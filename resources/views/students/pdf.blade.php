<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students List Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0 0 5px 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            color: #64748b;
            margin: 0;
            font-size: 11px;
        }
        .meta {
            margin-bottom: 15px;
            font-size: 11px;
            color: #475569;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            display: inline-block;
            margin-right: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Student Management System</h1>
        <p>Complete Students & Enrolled Courses Report</p>
    </div>

    <div class="meta">
        <span><strong>Generated Date:</strong> {{ date('Y-m-d H:i:s') }}</span> |
        <span><strong>Total Students:</strong> {{ $students->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th style="width: 40px;">Age</th>
                <th>Enrolled Courses</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td><strong>{{ $student->name }}</strong></td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone ?? 'N/A' }}</td>
                    <td>{{ $student->age ?? 'N/A' }}</td>
                    <td>
                        @if($student->courses->count() > 0)
                            @foreach($student->courses as $course)
                                <span class="badge">{{ $course->name }}</span>
                            @endforeach
                        @else
                            <span style="color: #94a3b8; font-style: italic;">No Courses</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">No students found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated automatically by Student Management System &copy; {{ date('Y') }}</p>
    </div>

</body>
</html>
