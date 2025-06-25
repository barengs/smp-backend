<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $santri = Student::where("status", 'Aktif')->count();
        $asatidz = Employee::count();
        $tugasan = Student::where("status", 'Tugas')->count();
        $alumni = Student::where("status", 'Alumni')->count();

        return response()->json([
            'santri' => $santri,
            'asatidz' => $asatidz,
            'tugasan' => $tugasan,
            'alumni' => $alumni
        ], 200);
    }
}
