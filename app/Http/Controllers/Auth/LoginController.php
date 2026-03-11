<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        // =========================
        // admin ثابت
        // =========================
        $adminEmail = 'admin';
        $adminPassword = 'admin123';

        if ($request->identifier === $adminEmail && $request->password === $adminPassword) {

            $admin = User::where('email', $adminEmail)->first();

            if (!$admin) {
                $admin = User::create([
                    'name' => 'Admin',
                    'email' => $adminEmail,
                    'role' => 'admin',
                    'password' => Hash::make($adminPassword),
                    'status' => 'active',
                ]);
            }

            Auth::login($admin);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        // =========================
        // دخول عادي
        // =========================
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'university_id';

        $user = User::where($field, $request->identifier)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'بيانات الدخول غير صحيحة')->withInput();
        }

        if ($user->status !== 'active') {
            return back()->with('error', 'حسابك قيد المراجعة أو مرفوض')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        // =========================
        // التحويل حسب role
        // =========================
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        if ($user->role === 'supervisor') {
            return redirect()->route('supervisor.dashboard');
        }

        if ($user->role === 'company') {
            return redirect()->route('company.dashboard');
        }

        Auth::logout();

        return redirect()->route('login')->with('error', 'نوع المستخدم غير مسموح');
    }
}
