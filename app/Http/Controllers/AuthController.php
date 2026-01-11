<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // إذا كان المستخدم مسجل دخول، أعد توجيهه للصفحة الرئيسية
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // إعادة إنشاء الجلسة لمنع session fixation
            $request->session()->regenerate();

            // رسالة نجاح
            return redirect()->intended(route('home'))
                ->with('success', 'تم تسجيل الدخول بنجاح');
        }

        // في حالة فشل تسجيل الدخول
        throw ValidationException::withMessages([
            'email' => 'البيانات المدخلة غير صحيحة',
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        // تسجيل خروج المستخدم
        Auth::guard('web')->logout();

        // إلغاء الجلسة
        $request->session()->invalidate();

        // إعادة إنشاء CSRF token
        $request->session()->regenerateToken();

        // إعادة التوجيه لصفحة تسجيل الدخول
        return redirect()->route('login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }
}