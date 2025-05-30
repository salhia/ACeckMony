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

        $notification = array(
            'message' => 'Login Successfully',
            'alert-type' => 'success'
        );

        if (auth()->user()->role == 'admin') {

          //  dd(auth()->user()->role);
            return redirect()->intended(RouteServiceProvider::ADMIN);
            //return redirect()->intended('/admin/dashboard')->with($notification);

        } elseif (auth()->user()->role == 'agent') {
             return redirect()->intended(RouteServiceProvider::AGENT);
        } elseif (auth()->user()->role == 'user') {
               return redirect()->intended(RouteServiceProvider::USER);
        }

       // return redirect()->intended(RouteServiceProvider::ADMIN);
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
        return redirect('/login')->with($notification);
    }
}
