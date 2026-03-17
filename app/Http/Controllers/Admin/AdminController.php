<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
    }



    public function adminUsersPage()
    {
        $this->ensureAdmin();

        $supervisors = User::where('role', 'supervisor')->latest()->get();

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


    public function adminDashboard()
    {
        $this->ensureAdmin();

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
    //

    

    public function adminCompaniesPage()
    {
        $this->ensureAdmin();

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

    public function updateSupervisorStatus(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'action' => ['required', 'in:active,reject,delete'],
        ]);

        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();

        if ($validated['action'] === 'delete') {
            $supervisor->delete();

            return back()->with('success', 'Supervisor deleted successfully.');
        }

        $supervisor->status = $validated['action'] === 'active' ? 'active' : 'rejected';
        $supervisor->save();

        return back()->with('success', 'Supervisor status updated successfully.');
    }

    public function updateCompanyStatus(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'action' => ['required', 'in:active,reject,delete'],
        ]);

        $company = User::where('id', $id)->where('role', 'company')->firstOrFail();

        if ($validated['action'] === 'delete') {
            $company->delete();

            return back()->with('success', 'Company deleted successfully.');
        }

        $company->status = $validated['action'] === 'active' ? 'active' : 'rejected';
        $company->save();

        return back()->with('success', 'Company status updated successfully.');
    }
}
