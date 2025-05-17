<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $notification = array();

        if (Auth::check()) {
            $user = Auth::user();
            $notification = [
                'message' => $user->name . ' تم تسجيل الدخول بنجاح',
                'alert-type' => 'success'
            ];

            switch($user->role) {
                case 'admin':
                    return redirect('/admin/dashboard')->with($notification);
                case 'agent':

                    return redirect('/agent/dashboard')->with($notification);
                case 'user':
                    return redirect('/user/dashboard')->with($notification);
                default:
                    return redirect('/')->with($notification);
            }
        }

        $notification = [
            'message' => 'فشل تسجيل الدخول',
            'alert-type' => 'error'
        ];
        return redirect()->route('login')->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Add a success message
        $notification = array(
            'message' => 'Logged Out Successfully',
            'alert-type' => 'success'
        );

        // Redirect to login page with notification
        return redirect('/')->with($notification);
    }
}
