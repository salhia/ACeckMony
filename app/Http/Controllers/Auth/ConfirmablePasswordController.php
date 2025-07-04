<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        if (auth()->user()->role == 'admin') {
            return redirect()->intended(RouteServiceProvider::ADMIN);
        } elseif (auth()->user()->role == 'agent') {
            return redirect()->intended(RouteServiceProvider::AGENT);
        } elseif (auth()->user()->role == 'user') {
            return redirect()->intended(RouteServiceProvider::USER);
        }

        return redirect()->intended(RouteServiceProvider::USER); // Default fallback
    }
}
