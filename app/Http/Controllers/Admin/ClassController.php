<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClassController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        return view('admin.classes.index');
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = ClassRoom::withCount('students')->select('classes.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('class_name', function ($c) {
                $section = $c->section
                    ? '<span class="dt-sub" style="font-size:.78rem;color:var(--muted)"> &mdash; ' . e($c->section) . '</span>'
                    : '';
                return '<div style="font-weight:500;color:var(--text)">' . e($c->name) . $section . '</div>';
            })
            ->addColumn('section', fn($c) => e($c->section ?? '—'))
            ->addColumn('students_count', function ($c) {
                return '<span class="badge" style="background:rgba(99,102,241,.15);color:var(--primary-lt);'
                    . 'border-radius:20px;padding:3px 10px;font-size:.78rem">'
                    . $c->students_count . '</span>';
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
            ->rawColumns(['class_name', 'students_count', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'section' => 'nullable|string|max:50',
            'status'  => 'nullable|boolean',
        ]);

        $class = ClassRoom::create([
            'name'    => $request->name,
            'section' => $request->section ?: null,
            'status'  => true,
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
                'id'      => $class->id,
                'name'    => $class->name,
                'section' => $class->section,
                'status'  => $class->status,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:100',
            'section' => 'nullable|string|max:50',
        ]);

        $class->update([
            'name'    => $request->name,
            'section' => $request->section ?: null,
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
