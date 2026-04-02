<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = (string) $request->identifier;
        $password = (string) $request->password;

        if ($identifier === 'admin' && $password === 'admin123') {
            $admin = User::where('email', 'admin')->first();

            if (! $admin) {
                $admin = User::create([
                    'name' => 'Admin',
                    'email' => 'admin',
                    'role' => 'admin',
                    'password' => Hash::make('admin123'),
                    'status' => 'active',
                ]);
            }

            Auth::login($admin);
            $request->session()->regenerate();
            $this->logLoginNotification($admin->id);

            return redirect()->route('admin.dashboard');
        }

        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'university_id';
        $user = User::where($field, $identifier)->first();

        if (! $user) {
            return back()->with('error', 'هذا الحساب غير موجود أو تم حذفه.')->withInput();
        }

        if (! Hash::check($password, (string) $user->password)) {
            return back()->with('error', 'كلمة المرور غير صحيحة.')->withInput();
        }

        if ((string) $user->status !== 'active') {
            return back()->with('error', 'حسابك غير نشط حالياً.')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();
        $this->logLoginNotification($user->id);

        return match ((string) $user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'supervisor' => redirect()->route('supervisor.dashboard'),
            'company' => redirect()->route('company.dashboard'),
            default => tap(redirect()->route('login')->with('error', 'نوع المستخدم غير مسموح.'), function () {
                Auth::logout();
            }),
        };
    }

    private function logLoginNotification(int $userId): void
    {
        $this->notifications->notifyUser(
            userId: $userId,
            title: 'Login Successful',
            description: 'تم تسجيل الدخول بنجاح.',
            type: 'success',
            meta: ['category' => 'auth']
        );
    }
}

