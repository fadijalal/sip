<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'role' => 'required|in:student,supervisor,company,admin',
            'name' => 'required|string|max:255',
            'password' => 'required|confirmed|min:6',
            'phone_number' => 'required|string|min:9',
            'email' => 'nullable|email|unique:users,email',
        ];

        if ($request->role === 'student') {
            $rules['university_id'] = 'required|string|unique:users,university_id';
            $rules['supervisor_code'] = 'required|exists:users,supervisor_code';
        }

        if ($request->role === 'supervisor') {
            $rules['university_id'] = 'required|string|unique:users,university_id';
        }

        if ($request->role === 'company') {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['company_name'] = 'required|string|max:255';
            $rules['company_address'] = 'required|string|max:255';
        }

        if ($request->role === 'admin') {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $request->validate($rules);

        $supervisorCode = null;

        if ($request->role === 'supervisor') {
            $supervisorCode = $this->generateSupervisorCode();
        }

        if ($request->role === 'student') {
            $supervisor = User::where('supervisor_code', $request->supervisor_code)
                ->where('role', 'supervisor')
                ->first();

            if (!$supervisor) {
                return back()->withErrors([
                    'supervisor_code' => 'كود المشرف غير صحيح'
                ])->withInput();
            }
        }

        $user = User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'university_id' => $request->university_id,
            'supervisor_code' => $request->role === 'supervisor'
                ? $supervisorCode
                : $request->supervisor_code,
            'company_name' => $request->company_name,
            'company_website' => $request->company_website,
            'company_address' => $request->company_address,
            'status' => $request->role === 'admin' ? 'active' : 'pending',
        ]);

        if ($user->role === 'admin' && $user->status === 'active') {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', 'تم إنشاء حساب الأدمن وتسجيل الدخول بنجاح');
        }

        return redirect()->route('login')
            ->with('success', 'تم إنشاء الحساب بنجاح، بانتظار موافقة الأدمن');
    }

    private function generateSupervisorCode(): string
    {
        do {
            $code = 'SUP-' . strtoupper(Str::random(6));
        } while (User::where('supervisor_code', $code)->exists());

        return $code;
    }
}
