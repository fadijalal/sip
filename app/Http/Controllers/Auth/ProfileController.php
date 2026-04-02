<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private function profilePayload(User $user): array
    {
        $role = (string) $user->role;

        $editable = [];
        if (in_array($role, ['student', 'supervisor'], true)) {
            $editable = ['name', 'phone_number'];
        } elseif ($role === 'company') {
            $editable = ['name', 'phone_number', 'company_name', 'company_address', 'company_website'];
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'phone_number' => $user->phone_number,
                'company_name' => $user->company_name,
                'company_address' => $user->company_address,
                'company_website' => $user->company_website,
                'status' => $user->status,
                'supervisor_code' => $user->supervisor_code,
                'university_id' => $user->university_id,
                'student_id' => $user->student_id ?? null,
            ],
            'editable_fields' => $editable,
        ];
    }

    public function edit(Request $request)
    {
         $user = Auth::user();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $this->profilePayload($user),
            ]);
        }

        return view('spa');
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin profile is read-only.',
            ], 422);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];

        if ($user->role === 'company') {
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
            $rules['company_address'] = ['nullable', 'string', 'max:255'];
            $rules['company_website'] = ['nullable', 'url', 'max:255'];
        }

        $validated = $request->validate($rules);

        if ($user->role !== 'company') {
            unset($validated['company_name'], $validated['company_address'], $validated['company_website']);
        }

        $user->update([
            'name' => trim((string) ($validated['name'] ?? $user->name)),
            'phone_number' => $validated['phone_number'] ? trim((string) $validated['phone_number']) : null,
            'company_name' => array_key_exists('company_name', $validated)
                ? ($validated['company_name'] ? trim((string) $validated['company_name']) : null)
                : $user->company_name,
            'company_address' => array_key_exists('company_address', $validated)
                ? ($validated['company_address'] ? trim((string) $validated['company_address']) : null)
                : $user->company_address,
            'company_website' => array_key_exists('company_website', $validated)
                ? ($validated['company_website'] ? trim((string) $validated['company_website']) : null)
                : $user->company_website,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => $this->profilePayload($user->fresh()),
        ]);
    }

    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! in_array((string) $user->role, ['student', 'supervisor', 'company'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This account type cannot change password from this screen.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'different:current_password'],
            'new_password_confirmation' => ['nullable', 'string'],
            'confirm_password' => ['nullable', 'string'],
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.different' => 'New password must be different from current password.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $currentPassword = (string) $request->input('current_password');
        $newPassword = (string) $request->input('new_password');
        $newPasswordConfirmation = (string) ($request->input('new_password_confirmation') ?: $request->input('confirm_password'));

        if ($newPasswordConfirmation === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Password confirmation is required.',
            ], 422);
        }

        if (! hash_equals($newPassword, $newPasswordConfirmation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password confirmation does not match.',
            ], 422);
        }

        $storedPassword = (string) $user->password;
        $currentMatches = false;

        if ($storedPassword === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is not set for this account.',
            ], 422);
        }

        if ($storedPassword !== '') {
            try {
                $currentMatches = Hash::check($currentPassword, $storedPassword)
                    || password_verify($currentPassword, $storedPassword)
                    || hash_equals($storedPassword, $currentPassword);
            } catch (\Throwable) {
                $currentMatches = hash_equals($storedPassword, $currentPassword);
            }
        }

        if (! $currentMatches) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! in_array((string) $user->role, ['student', 'supervisor', 'company'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This account type cannot be deleted.',
            ], 422);
        }

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Hard delete as requested: cascades remove related rows.
        $user->delete();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully.',
        ]);
    }
}
