<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
class AuthController extends Controller
{
    // بجيب جميع المستخدمين
    public function getAllUsers()
    {
        return response()->json([
            'status' => true,
            'data' => User::all()
        ]);
    }

    public function adminDashboard()
    {
        $totalUsers = User::whereIn('role', ['student', 'supervisor'])->count();
        $totalCompanies = User::where('role', 'company')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalCompanies'));
    }

  
 
 

  

 
}
