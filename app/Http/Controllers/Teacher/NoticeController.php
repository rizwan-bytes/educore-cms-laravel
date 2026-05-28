<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('author')
            ->where('status', true)
            ->whereIn('target_role', ['teacher', 'all'])
            ->latest()
            ->paginate(15);

        return view('teacher.notices', compact('notices'));
    }
}
