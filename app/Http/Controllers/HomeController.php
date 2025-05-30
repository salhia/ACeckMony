<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function homeRedirect()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'agent') {
            return redirect()->route('agent.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('user.dashboard');
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
