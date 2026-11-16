<?php

namespace App\Http\Controllers;

use App\Mail\DobleFactorMail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    // ==========================================
    // 1. REGISTRO DE USUARIOS
    // ==========================================
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un formato de correo válido.',
            'email.unique' => 'Este correo ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'empleado', // Rol seguro por defecto
        ]);

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (Exception $e) {
            // Si el servidor SMTP falla, permitimos el acceso pero avisamos
            Auth::login($user);
            return redirect()->route('home')->with('warning', 'Cuenta creada, pero no se pudo enviar el correo de bienvenida.');
        }

        Auth::login($user);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente! Bienvenido al sistema.');
    }

    // ==========================================
    // 2. INICIO DE SESIÓN (BIFURCACIÓN POR ROL)
    // ==========================================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'El formato del correo no es válido.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // CAMINO A: Administrador -> Proceso de 3 etapas (2FA a 5 minutos)
            if ($user->rol === 'admin') {
                // Candado: limpiamos cualquier bandera previa de 2FA
                $request->session()->forget('2fa_verified');

                $codigo = (string) random_int(100000, 999999);
                $expiracion = now()->addMinutes(5); // Regla estricta: máximo 5 minutos

                $user->two_factor_code = $codigo;
                $user->two_factor_expires_at = $expiracion;
                $user->save();

                $urlFirmada = URL::temporarySignedRoute(
                    'admin.2fa.verificar.enlace',
                    $expiracion,
                    ['user' => $user->id]
                );

                try {
                    Mail::to($user->email)->send(new DobleFactorMail($codigo, $urlFirmada));
                } catch (Exception $e) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'email' => 'Error al enviar el código de seguridad al correo. Verifica la conexión SMTP.',
                    ])->onlyInput('email');
                }

                return redirect()->route('admin.2fa.vista');
            }

            // CAMINO B: Empleado (y demás roles operativos) -> Acceso directo
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // ==========================================
    // 3. VERIFICACIÓN DOBLE FACTOR (2FA ADMIN)
    // ==========================================
    public function show2faForm()
    {
        $user = Auth::user();

        // Si no es admin o ya completó el 2FA, lo mandamos al panel
        if ($user->rol !== 'admin' || session('2fa_verified')) {
            return redirect()->route('home');
        }

        // Evitamos que el navegador guarde esta pantalla en caché (botón Atrás)
        return response()->view('auth.2fa')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function verificarCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|digits:6',
        ], [
            'codigo.required' => 'Debes ingresar el código de 6 dígitos.',
            'codigo.digits' => 'El código debe contener exactamente 6 números.',
        ]);

        $user = Auth::user();

        // Validar expiración de 5 minutos
        if (!$user->two_factor_expires_at || now()->greaterThan($user->two_factor_expires_at)) {
            return $this->cerrarSesionPorExpiracion($request);
        }

        if ($user->two_factor_code === $request->codigo) {
            return $this->aprobarAccesoAdmin($user);
        }

        return back()->withErrors([
            'codigo' => 'El código ingresado es incorrecto.',
        ])->onlyInput('codigo');
    }

    public function verificarEnlace(Request $request, $user)
    {
        if (!$request->hasValidSignature() || (int) $user !== (int) Auth::id()) {
            return redirect()->route('admin.2fa.vista')
                ->with('error', 'El enlace de verificación no es válido o pertenece a otra sesión.');
        }

        $usuarioActual = Auth::user();

        if (!$usuarioActual->two_factor_expires_at || now()->greaterThan($usuarioActual->two_factor_expires_at)) {
            return $this->cerrarSesionPorExpiracion($request);
        }

        return $this->aprobarAccesoAdmin($usuarioActual);
    }

    private function aprobarAccesoAdmin(User $user)
    {
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        session(['2fa_verified' => true]);

        return redirect()->route('home')->with('success', 'Verificación completada. Bienvenido, Administrador.');
    }

    private function cerrarSesionPorExpiracion(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()
            ->view('auth.login', ['error_expirado' => 'El código de verificación expiró (límite de 5 minutos). Inicia sesión nuevamente.'])
            ->setStatusCode(409);
    }

    // ==========================================
    // 4. CIERRE DE SESIÓN
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}