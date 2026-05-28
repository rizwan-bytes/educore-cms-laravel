<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class NoticeController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        return view('admin.notices.index');
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Notice::with('author')->select('notices.*');

        // Filter by target role
        if ($request->filled('target_role') && $request->target_role !== 'all') {
            $query->where('target_role', $request->target_role);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title', function ($n) {
                $preview = mb_strlen($n->content) > 80
                    ? mb_substr($n->content, 0, 80) . '...'
                    : $n->content;
                return '<div>'
                    . '<div style="font-weight:500;color:var(--text)">' . e($n->title) . '</div>'
                    . '<div class="dt-sub">' . e($preview) . '</div>'
                    . '</div>';
            })
            ->addColumn('target', function ($n) {
                $colors = [
                    'all'     => 'rgba(99,102,241,.15)|var(--primary-lt)',
                    'admin'   => 'rgba(239,68,68,.12)|#ef4444',
                    'teacher' => 'rgba(6,182,212,.12)|#06b6d4',
                    'student' => 'rgba(16,185,129,.12)|#10b981',
                ];
                [$bg, $color] = explode('|', $colors[$n->target_role] ?? $colors['all']);
                $label = match ($n->target_role) {
                    'all'     => __('notices.role_all'),
                    'admin'   => __('notices.role_admin'),
                    'teacher' => __('notices.role_teacher'),
                    'student' => __('notices.role_student'),
                    default   => $n->target_role,
                };
                return '<span class="badge" style="background:' . $bg . ';color:' . $color . ';'
                    . 'border-radius:20px;padding:3px 10px;font-size:.78rem">' . $label . '</span>';
            })
            ->addColumn('posted_by', function ($n) {
                return $n->author ? e($n->author->name) : '—';
            })
            ->addColumn('posted_on', fn($n) => $n->created_at->format('d M Y'))
            ->addColumn('status', function ($n) {
                $cls = $n->status ? 'active' : 'inactive';
                $lbl = $n->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" '
                    . 'onclick="toggleStatus(' . $n->id . ')" '
                    . 'title="' . __('common.click_to_toggle') . '">'
                    . $lbl . '</button>';
            })
            ->addColumn('actions', function ($n) {
                return '<div class="dt-actions">'
                    . '<button class="btn-icon edit" onclick="editNotice(' . $n->id . ')" title="' . __('common.edit') . '">'
                    . '<i class="fas fa-pen"></i></button>'
                    . '<button class="btn-icon delete" onclick="deleteNotice(' . $n->id . ')" title="' . __('common.delete') . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['title', 'target', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,admin,teacher,student',
        ]);

        Notice::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_role' => $request->target_role,
            'status'      => true,
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('notices.added_success'),
        ]);
    }

    // ── Edit (Ajax — load data for modal) ────────────────────────────────
    public function edit($id)
    {
        $notice = Notice::findOrFail($id);

        return response()->json([
            'notice' => [
                'id'          => $notice->id,
                'title'       => $notice->title,
                'content'     => $notice->content,
                'target_role' => $notice->target_role,
                'status'      => $notice->status,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:200',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,admin,teacher,student',
        ]);

        $notice->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_role' => $request->target_role,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('notices.updated_success'),
        ]);
    }

    // ── Destroy (Ajax) ───────────────────────────────────────────────────
    public function destroy($id)
    {
        Notice::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => __('notices.deleted_success'),
        ]);
    }

    // ── Toggle Status (Ajax) ─────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->update(['status' => !$notice->status]);

        return response()->json([
            'success' => true,
            'message' => __('notices.toggled_success'),
            'status'  => $notice->status,
        ]);
    }
}
