<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('author')
            ->where('status', true)
            ->whereIn('target_role', ['student', 'all'])
            ->latest()
            ->paginate(15);

        return view('student.notices', compact('notices'));
    }
}
