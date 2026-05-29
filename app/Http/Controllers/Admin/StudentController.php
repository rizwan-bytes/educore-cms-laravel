<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        $classes = ClassRoom::orderBy('name')->get();
        return view('admin.students.index', compact('classes'));
    }

    // ── DataTables (server-side) ─────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Student::with(['user', 'class'])
            ->leftJoin('users',   'users.id',   '=', 'students.user_id')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->select('students.*');

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('students.class_id', $request->class_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', function ($s) {
                $avatar = $s->user->avatar
                    ? '<img src="' . asset('storage/' . $s->user->avatar) . '" class="dt-avatar" alt="">'
                    : '<div class="dt-avatar-placeholder">' . strtoupper(substr($s->user->name, 0, 1)) . '</div>';
                return '<div class="dt-name-cell">'
                    . $avatar
                    . '<div><div class="dt-name">' . e($s->user->name) . '</div>'
                    . '<div class="dt-email">' . e($s->user->email) . '</div></div>'
                    . '</div>';
            })
            ->addColumn('class', function ($s) {
                $section = $s->class->section ? ' — ' . $s->class->section : '';
                return e($s->class->name . $section);
            })
            ->addColumn('guardian', function ($s) {
                $phone = $s->guardian_phone
                    ? '<div class="dt-sub">' . e($s->guardian_phone) . '</div>'
                    : '';
                return '<div>' . e($s->guardian_name ?? '—') . '</div>' . $phone;
            })
            ->addColumn('status', function ($s) {
                $cls = $s->status ? 'active' : 'inactive';
                $lbl = $s->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" onclick="toggleStatus(' . $s->id . ')" title="' . __('common.click_to_toggle') . '">' . $lbl . '</button>';
            })
            ->addColumn('actions', function ($s) {
                return '<div class="dt-actions">'
                    . '<button class="btn-icon edit" onclick="editStudent(' . $s->id . ')" title="' . __('common.edit') . '">'
                    . '<i class="fas fa-pen"></i></button>'
                    . '<button class="btn-icon delete" onclick="deleteStudent(' . $s->id . ')" title="' . __('common.delete') . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['name', 'guardian', 'status', 'actions'])
            ->make(true);
    }

    // ── Store (Ajax) ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'roll_no'         => 'nullable|string|max:30|unique:students,roll_no',
            'class_id'        => 'required|exists:classes,id',
            'gender'          => 'required|in:Male,Female',
            'guardian_name'   => 'nullable|string|max:100',
            'guardian_phone'  => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'date_of_birth'   => 'nullable|date',
            'admission_date'  => 'nullable|date',
            'photo'           => 'nullable|file|mimes:jpeg,png,webp|max:3072',
        ]);

        DB::beginTransaction();
        try {
            // Handle photo upload
            $avatar = null;
            if ($request->hasFile('photo')) {
                $avatar = $request->file('photo')->store('avatars', 'public');
            }

            // Create user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'student',
                'status'   => 'active',
                'avatar'   => $avatar,
            ]);

            // Create student record
            $user->student()->create([
                'class_id'       => $request->class_id,
                'roll_no'        => $request->roll_no,
                'guardian_name'  => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'address'        => $request->address,
                'date_of_birth'  => $request->date_of_birth ?: null,
                'gender'         => $request->gender,
                'admission_date' => $request->admission_date ?: null,
                'status'         => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('students.added_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('common.error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Edit (Ajax — load data into modal) ───────────────────────────────
    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);

        return response()->json([
            'student' => [
                'id'             => $student->id,
                'name'           => $student->user->name,
                'email'          => $student->user->email,
                'roll_no'        => $student->roll_no,
                'class_id'       => $student->class_id,
                'gender'         => $student->gender,
                'guardian_name'  => $student->guardian_name,
                'guardian_phone' => $student->guardian_phone,
                'address'        => $student->address,
                'date_of_birth'  => $student->date_of_birth?->format('Y-m-d'),
                'admission_date' => $student->admission_date?->format('Y-m-d'),
                'avatar'         => $student->user->avatar
                    ? asset('storage/' . $student->user->avatar)
                    : null,
            ],
        ]);
    }

    // ── Update (Ajax) ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($student->user_id)],
            'password'       => 'nullable|string|min:6',
            'roll_no'        => ['nullable', 'string', 'max:30', Rule::unique('students', 'roll_no')->ignore($id)],
            'class_id'       => 'required|exists:classes,id',
            'gender'         => 'required|in:Male,Female',
            'guardian_name'  => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'date_of_birth'  => 'nullable|date',
            'admission_date' => 'nullable|date',
            'photo'          => 'nullable|file|mimes:jpeg,png,webp|max:3072',
        ]);

        DB::beginTransaction();
        try {
            // Handle photo
            $avatar = $student->user->avatar;
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($avatar) Storage::disk('public')->delete($avatar);
                $avatar = $request->file('photo')->store('avatars', 'public');
            }

            // Update user
            $userData = [
                'name'   => $request->name,
                'email'  => $request->email,
                'avatar' => $avatar,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $student->user->update($userData);

            // Update student
            $student->update([
                'class_id'       => $request->class_id,
                'roll_no'        => $request->roll_no,
                'guardian_name'  => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'address'        => $request->address,
                'date_of_birth'  => $request->date_of_birth ?: null,
                'gender'         => $request->gender,
                'admission_date' => $request->admission_date ?: null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('students.updated_success'),
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
        $student = Student::with('user')->findOrFail($id);

        // Delete avatar from storage
        if ($student->user->avatar) {
            Storage::disk('public')->delete($student->user->avatar);
        }

        // Deleting the user cascades to student via User::boot()
        $student->user->delete();

        return response()->json([
            'success' => true,
            'message' => __('students.deleted_success'),
        ]);
    }

    // ── Toggle Status (Ajax) ─────────────────────────────────────────────
    public function toggleStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['status' => !$student->status]);

        return response()->json([
            'success' => true,
            'message' => __('students.toggled_success'),
            'status'  => $student->status,
        ]);
    }
}
