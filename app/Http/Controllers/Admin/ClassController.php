<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClassController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        $teachers = Teacher::with('user')
            ->where('status', true)
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->user->name ?? '—']);

        return view('admin.classes.index', compact('teachers'));
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = ClassRoom::with('inchargeTeacher.user')
            ->withCount('students')
            ->select('classes.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('class_name', function ($c) {
                $section = $c->section
                    ? '<span class="dt-sub" style="font-size:.78rem;color:var(--muted)"> &mdash; ' . e($c->section) . '</span>'
                    : '';
                return '<div style="font-weight:500;color:var(--text)">' . e($c->name) . $section . '</div>';
            })
            ->addColumn('students_count', function ($c) {
                return '<span class="badge" style="background:rgba(99,102,241,.15);color:var(--primary-lt);'
                    . 'border-radius:20px;padding:3px 10px;font-size:.78rem">'
                    . $c->students_count . '</span>';
            })
            ->addColumn('att_mode', function ($c) {
                if ($c->attendance_mode === 'class_incharge') {
                    $incharge = $c->inchargeTeacher?->user?->name ?? __('classes.no_incharge');
                    return '<div>'
                        . '<span style="background:rgba(6,182,212,.12);color:var(--cyan);padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:500">'
                        . '<i class="fas fa-user-tie me-1"></i>' . __('classes.mode_class_incharge') . '</span>'
                        . '<div style="font-size:.75rem;color:var(--muted);margin-top:2px">' . e($incharge) . '</div>'
                        . '</div>';
                } else {
                    return '<span style="background:rgba(245,158,11,.12);color:#f59e0b;padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:500">'
                        . '<i class="fas fa-book-open me-1"></i>' . __('classes.mode_subject_wise') . '</span>';
                }
            })
            ->addColumn('status', function ($c) {
                $cls = $c->status ? 'active' : 'inactive';
                $lbl = $c->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" '
                    . 'onclick="toggleStatus(' . $c->id . ')" '
                    . 'title="' . __('common.click_to_toggle') . '">'
                    . $lbl . '</button>';
            })
            ->addColumn('actions', function ($c) {
                return '<div class="dt-actions">'
                    . '<button class="btn-icon edit" onclick="editClass(' . $c->id . ')" title="' . __('common.edit') . '">'
                    . '<i class="fas fa-pen"></i></button>'
                    . '<button class="btn-icon delete" onclick="deleteClass(' . $c->id . ')" title="' . __('common.delete') . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['class_name', 'students_count', 'att_mode', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'section'             => 'nullable|string|max:50',
            'attendance_mode'     => 'required|in:class_incharge,subject_wise',
            'incharge_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class = ClassRoom::create([
            'name'                => $request->name,
            'section'             => $request->section ?: null,
            'attendance_mode'     => $request->attendance_mode,
            'incharge_teacher_id' => $request->attendance_mode === 'class_incharge'
                                        ? ($request->incharge_teacher_id ?: null)
                                        : null,
            'status'              => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('classes.added_success'),
            'data'    => $class,
        ]);
    }

    // ── Edit (Ajax — load data for modal) ────────────────────────────────
    public function edit($id)
    {
        $class = ClassRoom::findOrFail($id);

        return response()->json([
            'class' => [
                'id'                  => $class->id,
                'name'                => $class->name,
                'section'             => $class->section,
                'status'              => $class->status,
                'attendance_mode'     => $class->attendance_mode,
                'incharge_teacher_id' => $class->incharge_teacher_id,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name'                => 'required|string|max:100',
            'section'             => 'nullable|string|max:50',
            'attendance_mode'     => 'required|in:class_incharge,subject_wise',
            'incharge_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class->update([
            'name'                => $request->name,
            'section'             => $request->section ?: null,
            'attendance_mode'     => $request->attendance_mode,
            'incharge_teacher_id' => $request->attendance_mode === 'class_incharge'
                                        ? ($request->incharge_teacher_id ?: null)
                                        : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('classes.updated_success'),
        ]);
    }

    // ── Destroy (Ajax) ───────────────────────────────────────────────────
    public function destroy($id)
    {
        $class = ClassRoom::withCount('students')->findOrFail($id);

        if ($class->students_count > 0) {
            return response()->json([
                'success' => false,
                'message' => __('classes.cannot_delete'),
            ], 422);
        }

        $class->delete();

        return response()->json([
            'success' => true,
            'message' => __('classes.deleted_success'),
        ]);
    }

    // ── Toggle Status (Ajax) ─────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->update(['status' => !$class->status]);

        return response()->json([
            'success' => true,
            'message' => __('classes.toggled_success'),
            'status'  => $class->status,
        ]);
    }
}
