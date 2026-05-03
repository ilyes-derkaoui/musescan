<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    private function isAdminEmail(?string $email): bool
    {
        $adminEmail = (string) config('app.admin_email', 'admin@museum.local');

        return strtolower((string) $email) === strtolower($adminEmail);
    }

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (! $this->isAdminEmail(Auth::user()?->email)) {
                return redirect('/');
            }

            return redirect()->route('admin.artifacts.index');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! $this->isAdminEmail(Auth::user()?->email)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Accès admin uniquement.'])
                    ->onlyInput('email');
            }

            return redirect()->intended(route('admin.artifacts.index'));
        }

        return back()
            ->withErrors(['email' => 'Identifiants incorrects.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
