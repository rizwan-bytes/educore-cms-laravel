<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        $classes  = ClassRoom::orderBy('name')->get();
        $teachers = Teacher::with('user')->where('status', true)
                        ->get()->map(fn($t) => [
                            'id'   => $t->id,
                            'name' => $t->user->name ?? '—',
                        ]);

        return view('admin.subjects.index', compact('classes', 'teachers'));
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Subject::with(['class', 'teacher.user'])->select('subjects.*');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('subject_name', fn($s) =>
                '<div style="font-weight:500;color:var(--text)">' . e($s->name) . '</div>'
            )
            ->addColumn('code', fn($s) => $s->code
                ? '<code style="background:rgba(99,102,241,.12);color:var(--primary-lt);padding:2px 8px;border-radius:4px;font-size:.8rem">' . e($s->code) . '</code>'
                : '<span style="color:var(--muted)">—</span>')
            ->addColumn('class', function ($s) {
                $section = $s->class->section ? ' — ' . $s->class->section : '';
                return e($s->class->name . $section);
            })
            ->addColumn('teacher_name', function ($s) {
                return $s->teacher
                    ? '<span style="color:var(--cyan)">' . e($s->teacher->user->name ?? '—') . '</span>'
                    : '<span style="color:var(--muted)">—</span>';
            })
            ->addColumn('status', function ($s) {
                $cls = $s->status ? 'active' : 'inactive';
                $lbl = $s->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" '
                    . 'onclick="toggleStatus(' . $s->id . ')" '
                    . 'title="' . __('common.click_to_toggle') . '">'
                    . $lbl . '</button>';
            })
            ->addColumn('actions', function ($s) {
                return '<div class="dt-actions">'
                    . '<button class="btn-icon edit" onclick="editSubject(' . $s->id . ')" title="' . __('common.edit') . '">'
                    . '<i class="fas fa-pen"></i></button>'
                    . '<button class="btn-icon delete" onclick="deleteSubject(' . $s->id . ')" title="' . __('common.delete') . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['subject_name', 'code', 'teacher_name', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => 'nullable|string|max:20',
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $subject = Subject::create([
            'name'       => $request->name,
            'code'       => $request->code ?: null,
            'class_id'   => $request->class_id,
            'teacher_id' => $request->teacher_id ?: null,
            'status'     => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('subjects.added_success'),
            'data'    => $subject,
        ]);
    }

    // ── Edit (Ajax — load data for modal) ────────────────────────────────
    public function edit($id)
    {
        $subject = Subject::findOrFail($id);

        return response()->json([
            'subject' => [
                'id'         => $subject->id,
                'name'       => $subject->name,
                'code'       => $subject->code,
                'class_id'   => $subject->class_id,
                'teacher_id' => $subject->teacher_id,
                'status'     => $subject->status,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => 'nullable|string|max:20',
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $subject->update([
            'name'       => $request->name,
            'code'       => $request->code ?: null,
            'class_id'   => $request->class_id,
            'teacher_id' => $request->teacher_id ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('subjects.updated_success'),
        ]);
    }

    // ── Destroy (Ajax) ───────────────────────────────────────────────────
    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => __('subjects.deleted_success')]);
    }

    // ── Toggle Status (Ajax) ─────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update(['status' => !$subject->status]);
        return response()->json(['success' => true, 'message' => __('subjects.toggled_success'), 'status' => $subject->status]);
    }
}
