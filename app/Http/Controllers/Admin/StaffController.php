<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────
    public function index()
    {
        abort_unless(TenantService::canUse('staff_management'), 403,
            __('staff.feature_unavailable'));

        $departments = Staff::DEPARTMENTS;
        $totalStaff  = Staff::count();

        return view('admin.staff.index', compact('departments', 'totalStaff'));
    }

    // ── DataTable Data ───────────────────────────────────────────────────
    public function data(Request $request)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $query = Staff::query()->select('staff.*');

        // Department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        // Status filter
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status === '1');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('photo_name', function (Staff $s) {
                $url = $s->photo_url;
                return '<div class="dt-name-cell">
                    <img src="' . $url . '" class="dt-avatar" alt="' . e($s->name) . '">
                    <div>
                        <div class="dt-name">' . e($s->name) . '</div>
                        <div class="dt-sub">' . e($s->designation ?? '—') . '</div>
                    </div>
                </div>';
            })
            ->addColumn('dept_badge', function (Staff $s) {
                $colors = [
                    'administrative'   => 'var(--primary)',
                    'finance'          => 'var(--green)',
                    'academic_support' => 'var(--cyan)',
                    'support'          => 'var(--muted)',
                ];
                $color = $colors[$s->department] ?? 'var(--muted)';
                return '<span class="badge-dept" style="background:' . $color . '20;color:' . $color . ';border:1px solid ' . $color . '40;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">'
                     . e($s->department_label)
                     . '</span>';
            })
            ->addColumn('contact', function (Staff $s) {
                $phone = $s->phone ? '<div class="dt-sub"><i class="fas fa-phone fa-xs me-1"></i>' . e($s->phone) . '</div>' : '';
                $cnic  = $s->cnic  ? '<div class="dt-sub"><i class="fas fa-id-card fa-xs me-1"></i>' . e($s->cnic) . '</div>'  : '';
                return $phone . $cnic ?: '—';
            })
            ->addColumn('joining', function (Staff $s) {
                return $s->joining_date
                    ? $s->joining_date->format('d M Y')
                    : '—';
            })
            ->addColumn('salary_fmt', function (Staff $s) {
                return $s->salary
                    ? '<span style="color:var(--green);">₨' . number_format($s->salary, 0) . '</span>'
                    : '—';
            })
            ->addColumn('status_badge', function (Staff $s) {
                $cls  = $s->status ? 'active'   : 'inactive';
                $lbl  = $s->status ? __('common.active') : __('common.inactive');
                return '<button class="badge-status ' . $cls . '" onclick="toggleStaff(' . $s->id . ')">'
                     . $lbl . '</button>';
            })
            ->addColumn('actions', function (Staff $s) {
                return '<div class="dt-actions">
                    <button class="btn-icon edit"   onclick="editStaff(' . $s->id . ')"><i class="fas fa-pen"></i></button>
                    <button class="btn-icon delete" onclick="deleteStaff(' . $s->id . ')"><i class="fas fa-trash"></i></button>
                </div>';
            })
            ->rawColumns(['photo_name', 'dept_badge', 'contact', 'salary_fmt', 'status_badge', 'actions'])
            ->make(true);
    }

    // ── Store ────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'cnic'         => 'nullable|string|max:15|unique:staff,cnic',
            'department'   => 'required|in:administrative,finance,academic_support,support',
            'designation'  => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'salary'       => 'nullable|numeric|min:0',
            'photo'        => 'nullable|file|mimes:jpeg,png,webp|max:2048',
            'status'       => 'sometimes|boolean',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        Staff::create(array_merge($validated, [
            'photo'  => $photoPath,
            'status' => $request->boolean('status', true),
        ]));

        return response()->json([
            'success' => true,
            'message' => __('staff.added_success'),
        ]);
    }

    // ── Edit ─────────────────────────────────────────────────────────────
    public function edit(int $id)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $staff = Staff::findOrFail($id);

        return response()->json([
            'staff' => [
                'id'           => $staff->id,
                'name'         => $staff->name,
                'phone'        => $staff->phone,
                'cnic'         => $staff->cnic,
                'department'   => $staff->department,
                'designation'  => $staff->designation,
                'joining_date' => $staff->joining_date?->format('Y-m-d'),
                'salary'       => $staff->salary,
                'status'       => $staff->status ? 1 : 0,
                'photo_url'    => $staff->photo_url,
            ],
        ]);
    }

    // ── Update ───────────────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'cnic'         => 'nullable|string|max:15|unique:staff,cnic,' . $id,
            'department'   => 'required|in:administrative,finance,academic_support,support',
            'designation'  => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'salary'       => 'nullable|numeric|min:0',
            'photo'        => 'nullable|file|mimes:jpeg,png,webp|max:2048',
            'status'       => 'sometimes|boolean',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($staff->photo) {
                \Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('staff-photos', 'public');
        } else {
            unset($validated['photo']);
        }

        $staff->update(array_merge($validated, [
            'status' => $request->boolean('status', true),
        ]));

        return response()->json([
            'success' => true,
            'message' => __('staff.updated_success'),
        ]);
    }

    // ── Destroy ──────────────────────────────────────────────────────────
    public function destroy(int $id)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $staff = Staff::findOrFail($id);

        if ($staff->photo) {
            \Storage::disk('public')->delete($staff->photo);
        }

        $staff->delete();

        return response()->json(['message' => __('staff.deleted_success')]);
    }

    // ── Toggle Status ────────────────────────────────────────────────────
    public function toggleStatus(int $id)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $staff = Staff::findOrFail($id);
        $staff->update(['status' => !$staff->status]);

        return response()->json([
            'success' => true,
            'status'  => $staff->status,
            'message' => $staff->status ? __('staff.activated') : __('staff.deactivated'),
        ]);
    }
}
