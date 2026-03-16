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
        $totalAll = User::count();
        $currentDate = now()->format('M d, Y');

        $recentActivities = User::select('id', 'name', 'email', 'role', 'status', 'created_at', 'company_name')
            ->whereIn('role', ['company', 'supervisor'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCompanies',
            'totalAll',
            'currentDate',
            'recentActivities'
        ));
    }

    public function adminUsersPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $supervisors = User::where('role', 'supervisor')
            ->latest()
            ->get();

        $totalSupervisors = User::where('role', 'supervisor')->count();
        $approvedSupervisors = User::where('role', 'supervisor')->where('status', 'active')->count();
        $pendingSupervisors = User::where('role', 'supervisor')->where('status', 'pending')->count();
        $rejectedSupervisors = User::where('role', 'supervisor')->where('status', 'rejected')->count();

        return view('admin.users', compact(
            'supervisors',
            'totalSupervisors',
            'approvedSupervisors',
            'pendingSupervisors',
            'rejectedSupervisors'
        ));
    }

    public function adminCompaniesPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $companies = User::where('role', 'company')->latest()->get();

        $totalCompanies = User::where('role', 'company')->count();
        $approvedCompanies = User::where('role', 'company')->where('status', 'active')->count();
        $pendingCompanies = User::where('role', 'company')->where('status', 'pending')->count();

        return view('admin.companies', compact(
            'companies',
            'totalCompanies',
            'approvedCompanies',
            'pendingCompanies'
        ));
    }

    // فقط الشركات والمشرفين
    public function getFilteredUsers()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $users = User::select('id', 'name', 'email', 'role', 'status', 'created_at')
            ->whereIn('role', ['company', 'supervisor'])
            ->orderBy('id', 'desc')//ترتيب تنازلي
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
