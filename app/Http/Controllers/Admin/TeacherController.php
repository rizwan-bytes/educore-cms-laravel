<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        // Distinct subject names from subjects table for specialization dropdown
        $subjects = Subject::select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        return view('admin.teachers.index', compact('subjects'));
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Teacher::with('user')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select('teachers.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', function ($t) {
                $avatar = $t->user->avatar
                    ? '<img src="' . asset('storage/' . $t->user->avatar) . '" class="dt-avatar" alt="">'
                    : '<div class="dt-avatar-placeholder">' . strtoupper(substr($t->user->name, 0, 1)) . '</div>';

                $phone = $t->user->phone
                    ? '<div class="dt-sub"><i class="fas fa-phone fa-xs me-1" style="color:var(--muted)"></i>' . e($t->user->phone) . '</div>'
                    : '';

                return '<div class="dt-name-cell">'
                    . $avatar
                    . '<div>'
                    . '<div class="dt-name">' . e($t->user->name) . '</div>'
                    . '<div class="dt-email">' . e($t->user->email) . '</div>'
                    . $phone
                    . '</div>'
                    . '</div>';
            })
            ->addColumn('qualification', fn($t) => e($t->qualification ?? '—'))
            ->addColumn('specialization', fn($t) => e($t->subject_specialization ?? '—'))
            ->addColumn('joining_date', fn($t) => $t->joining_date
                ? $t->joining_date->format('d M Y')
                : '—')
            ->addColumn('status', function ($t) {
                $cls = $t->status ? 'active' : 'inactive';
                $lbl = $t->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" '
                    . 'onclick="toggleStatus(' . $t->id . ')" '
                    . 'title="' . __('common.click_to_toggle') . '">'
                    . $lbl . '</button>';
            })
            ->addColumn('actions', function ($t) {
                return '<div class="dt-actions">'
                    . '<button class="btn-icon edit" onclick="editTeacher(' . $t->id . ')" title="' . __('common.edit') . '">'
                    . '<i class="fas fa-pen"></i></button>'
                    . '<button class="btn-icon delete" onclick="deleteTeacher(' . $t->id . ')" title="' . __('common.delete') . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['name', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:100',
            'email'                  => 'required|email|unique:users,email',
            'password'               => 'required|string|min:6',
            'phone'                  => 'nullable|string|max:20',
            'qualification'          => 'nullable|string|max:100',
            'subject_specialization' => 'nullable|string|max:100',
            'joining_date'           => 'nullable|date',
            'photo'                  => 'nullable|file|mimes:jpeg,png,webp|max:3072',
        ]);

        DB::beginTransaction();
        try {
            $avatar = null;
            if ($request->hasFile('photo')) {
                $avatar = $request->file('photo')->store('avatars', 'public');
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'teacher',
                'status'   => 'active',
                'phone'    => $request->phone,
                'avatar'   => $avatar,
            ]);

            $user->teacher()->create([
                'qualification'          => $request->qualification,
                'subject_specialization' => $request->subject_specialization,
                'joining_date'           => $request->joining_date ?: null,
                'status'                 => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('teachers.added_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('common.error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Edit (Ajax — load data for modal) ────────────────────────────────
    public function edit($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        return response()->json([
            'teacher' => [
                'id'                     => $teacher->id,
                'name'                   => $teacher->user->name,
                'email'                  => $teacher->user->email,
                'phone'                  => $teacher->user->phone,
                'qualification'          => $teacher->qualification,
                'subject_specialization' => $teacher->subject_specialization,
                'joining_date'           => $teacher->joining_date?->format('Y-m-d'),
                'avatar'                 => $teacher->user->avatar
                    ? asset('storage/' . $teacher->user->avatar)
                    : null,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        $request->validate([
            'name'                   => 'required|string|max:100',
            'email'                  => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password'               => 'nullable|string|min:6',
            'phone'                  => 'nullable|string|max:20',
            'qualification'          => 'nullable|string|max:100',
            'subject_specialization' => 'nullable|string|max:100',
            'joining_date'           => 'nullable|date',
            'photo'                  => 'nullable|file|mimes:jpeg,png,webp|max:3072',
        ]);

        DB::beginTransaction();
        try {
            $avatar = $teacher->user->avatar;
            if ($request->hasFile('photo')) {
                if ($avatar) Storage::disk('public')->delete($avatar);
                $avatar = $request->file('photo')->store('avatars', 'public');
            }

            $userData = [
                'name'   => $request->name,
                'email'  => $request->email,
                'phone'  => $request->phone,
                'avatar' => $avatar,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $teacher->user->update($userData);

            $teacher->update([
                'qualification'          => $request->qualification,
                'subject_specialization' => $request->subject_specialization,
                'joining_date'           => $request->joining_date ?: null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('teachers.updated_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('common.error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Destroy (Ajax) ───────────────────────────────────────────────────
    public function destroy($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        if ($teacher->user->avatar) {
            Storage::disk('public')->delete($teacher->user->avatar);
        }

        // User::boot() cascade deletes teacher record
        $teacher->user->delete();

        return response()->json([
            'success' => true,
            'message' => __('teachers.deleted_success'),
        ]);
    }

    // ── Toggle Status (Ajax) ─────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['status' => !$teacher->status]);

        return response()->json([
            'success' => true,
            'message' => __('teachers.toggled_success'),
            'status'  => $teacher->status,
        ]);
    }
}
