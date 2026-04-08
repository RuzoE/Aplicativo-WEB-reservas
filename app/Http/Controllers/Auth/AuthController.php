<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Rules\AllowedEmailDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {

        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Display the login view.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Display the registration view.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        /* Auth::login($user); */

        return redirect()->route('login')->with('success', 'Cuenta creada exitosamente. Por favor, inicia sesión.');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            $fields = session('check_in');
            $request->session()->regenerate();
            session('check_in', $fields);
            if ($request->user()->is_admin) {
                return redirect()->route('admin.index');
            }
            return redirect()->intended('/');
        }

        $failedUser = User::where('email', $request->input('email'))->first();
        registrarAuditoria(
            'LOGIN_FAILED',
            'usuarios',
            $failedUser?->id,
            'Intento de inicio de sesion web fallido para el correo ' . $request->input('email') . ' desde IP ' . $request->ip(),
            $failedUser?->id,
            ['skip_duplicate' => false]
        );

        return back()->withErrors([
            'login_err' => __('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
