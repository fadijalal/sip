<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function adminDashboard()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $totalUsers = User::whereIn('role', ['student', 'supervisor'])->count();
        $totalCompanies = User::where('role', 'company')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalCompanies'));
    }

    public function adminUsersPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        return view('admin.users');
    }

    // فقط الشركات والمشرفين
    public function getFilteredUsers()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $users = User::select('id', 'name', 'email', 'role', 'status', 'created_at')
            ->whereIn('role', ['company', 'supervisor'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    // تفعيل / رفض / حذف مشرف
    public function updateSupervisorStatus(Request $request, $id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();

        $request->validate([
            'action' => 'required|in:active,reject,delete'
        ]);

        if ($request->action === 'active') {
            $supervisor->status = 'active';
            $supervisor->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل حساب المشرف'
            ], 200);
        }

        if ($request->action === 'reject') {
            $supervisor->status = 'rejected';
            $supervisor->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تعطيل حساب المشرف'
            ], 200);
        }

        if ($request->action === 'delete') {
            $supervisor->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف حساب المشرف'
            ], 200);
        }
    }

    // تفعيل / رفض / حذف شركة
    public function updateCompanyStatus(Request $request, $id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $company = User::where('id', $id)->where('role', 'company')->firstOrFail();

        $request->validate([
            'action' => 'required|in:active,reject,delete'
        ]);

        if ($request->action === 'active') {
            $company->status = 'active';
            $company->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل حساب الشركة'
            ], 200);
        }

        if ($request->action === 'reject') {
            $company->status = 'rejected';
            $company->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تعطيل الشركة'
            ], 200);
        }

        if ($request->action === 'delete') {
            $company->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الشركة'
            ], 200);
        }
    }
}
