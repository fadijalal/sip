<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function getAllUsers()
    {
        abort_unless(Auth::check(), 403);

        $users = User::latest()->get();

        return view('admin.users', [
            'supervisors' => $users->where('role', 'supervisor')->values(),
            'totalSupervisors' => $users->where('role', 'supervisor')->count(),
            'approvedSupervisors' => $users->where('role', 'supervisor')->where('status', 'active')->count(),
            'pendingSupervisors' => $users->where('role', 'supervisor')->where('status', 'pending')->count(),
            'rejectedSupervisors' => $users->where('role', 'supervisor')->where('status', 'rejected')->count(),
        ]);
    }
}
