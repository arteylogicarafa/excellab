<?php
class AuthController
{
    public function root(): void
    {
        redirect(Auth::check() ? Auth::home() : 'login');
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            redirect(Auth::home());
        }
        render('auth/login', [], 'auth');
    }

    public function staffForm(): void
    {
        if (Auth::check()) {
            redirect(Auth::home());
        }
        render('auth/staff', [], 'auth');
    }

    /** Entrada de estudiantes: solo el correo. Si no existe, se crea la cuenta automáticamente. */
    public function enter(): void
    {
        $email = strtolower(input('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            flash('danger', 'Escribe un correo electrónico válido.');
            remember_input(['email' => $email]);
            redirect('login');
        }
        $u = DB::one('SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=?', [$email]);
        if ($u && $u['role'] !== 'estudiante') {
            flash('warning', 'Ese correo pertenece a un docente. Usa el enlace «Acceso docentes».');
            redirect('login');
        }
        if ($u && $u['status'] !== 'active') {
            flash('danger', 'Tu acceso está deshabilitado. Consulta con tu docente.');
            redirect('login');
        }
        if (!$u) {
            // Límite sencillo contra altas masivas desde una misma sesión
            $_SESSION['new_accounts'] = ($_SESSION['new_accounts'] ?? 0) + 1;
            if ($_SESSION['new_accounts'] > 5) {
                flash('danger', 'Demasiados intentos. Inténtalo más tarde.');
                redirect('login');
            }
            $local = preg_replace('/[._\-+]+/', ' ', explode('@', $email)[0]);
            $name = mb_convert_case(trim(preg_replace('/\d+/', '', $local)) ?: 'Estudiante', MB_CASE_TITLE, 'UTF-8');
            $id = DB::insert('users', [
                'role_id' => 3, 'name' => mb_substr($name, 0, 120), 'email' => $email,
                'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'status' => 'active', 'must_change_password' => 0,
            ]);
            Notifier::send($id, 'Bienvenido a ExcelLab', 'Empieza por el módulo 1 "Introducción a Excel". Tu avance se guarda con tu correo.', 'aprender');
            Activity::log('alta_automatica', 'users', $id, $email, $id);
            $u = DB::one('SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?', [$id]);
        }
        $u['must_change_password'] = 0;
        clear_old();
        Auth::login($u);
        redirect('inicio');
    }

    public function login(): void
    {
        $email = strtolower(input('email'));
        $pass = (string)($_POST['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
            flash('danger', 'Escribe un correo válido y tu contraseña.');
            remember_input(['email' => $email]);
            redirect('docentes');
        }
        [$ok, $msg] = Auth::attempt($email, $pass);
        if (!$ok) {
            flash('danger', $msg);
            remember_input(['email' => $email]);
            redirect('docentes');
        }
        clear_old();
        if (Auth::user()['must_change']) {
            redirect('cuenta/password');
        }
        $intended = $_SESSION['intended'] ?? '';
        unset($_SESSION['intended']);
        $base = base_url();
        if ($intended && $intended[0] === '/' && !str_starts_with($intended, '//') && ($base === '' || str_starts_with($intended, $base . '/'))) {
            // Evita rutas de otro rol
            $path = substr($intended, strlen($base));
            if (!(str_starts_with($path, '/admin') && !Auth::isStaff())) {
                header('Location: ' . $intended);
                exit;
            }
        }
        redirect(Auth::home());
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'Cerraste sesión correctamente.');
        redirect('login');
    }

    // --- Recuperación de contraseña ---
    public function forgotForm(): void
    {
        render('auth/forgot', [], 'auth');
    }

    public function forgot(): void
    {
        $email = strtolower(input('email'));
        $user = filter_var($email, FILTER_VALIDATE_EMAIL)
            ? DB::one("SELECT id, name FROM users WHERE email=? AND status='active'", [$email]) : null;
        unset($_SESSION['dev_reset_link']);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            DB::insert('password_resets', [
                'user_id' => $user['id'], 'token_hash' => hash('sha256', $token),
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            ]);
            $scheme = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
            $link = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('restablecer/' . $token);
            $body = "Hola {$user['name']},\n\nPara restablecer tu contraseña de ExcelLab abre este enlace (válido por 1 hora):\n$link\n\nSi no lo solicitaste, ignora este mensaje.";
            if (config('mail_dev')) {
                @file_put_contents(ROOT . '/storage/logs/mail.log', '[' . date('Y-m-d H:i:s') . "] Para: $email\n$body\n\n", FILE_APPEND);
                $_SESSION['dev_reset_link'] = $link;
            } else {
                @mail($email, 'Restablecer contraseña - ExcelLab', $body);
            }
            Activity::log('recuperacion_solicitada', 'users', (int)$user['id'], null, (int)$user['id']);
        }
        flash('success', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.');
        redirect('recuperar');
    }

    private function validToken(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        return DB::one('SELECT * FROM password_resets WHERE token_hash=? AND used_at IS NULL AND expires_at > NOW()', [hash('sha256', $token)]);
    }

    public function resetForm(string $token): void
    {
        if (!$this->validToken($token)) {
            flash('danger', 'El enlace no es válido o ya expiró. Solicita uno nuevo.');
            redirect('recuperar');
        }
        render('auth/reset', ['token' => $token], 'auth');
    }

    public function reset(string $token): void
    {
        $r = $this->validToken($token);
        if (!$r) {
            flash('danger', 'El enlace no es válido o ya expiró. Solicita uno nuevo.');
            redirect('recuperar');
        }
        $err = $this->passwordError((string)($_POST['password'] ?? ''), (string)($_POST['password2'] ?? ''));
        if ($err) {
            flash('danger', $err);
            redirect('restablecer/' . $token);
        }
        DB::exec('UPDATE users SET password_hash=?, must_change_password=0, failed_attempts=0, locked_until=NULL, updated_at=NOW() WHERE id=?', [password_hash($_POST['password'], PASSWORD_DEFAULT), $r['user_id']]);
        DB::exec('UPDATE password_resets SET used_at=NOW() WHERE id=?', [$r['id']]);
        Activity::log('contrasena_restablecida', 'users', (int)$r['user_id'], null, (int)$r['user_id']);
        flash('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        redirect('docentes');
    }

    // --- Cuenta ---
    public function account(): void
    {
        $u = DB::one('SELECT u.*, r.label AS role_label FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?', [Auth::id()]);
        render('auth/account', ['u' => $u, 'forced' => Auth::user()['must_change']]);
    }

    public function updateProfile(): void
    {
        $name = input('name');
        if (mb_strlen($name) < 3 || mb_strlen($name) > 120) {
            flash('danger', 'El nombre debe tener entre 3 y 120 caracteres.');
            redirect('cuenta');
        }
        DB::exec('UPDATE users SET name=?, updated_at=NOW() WHERE id=?', [$name, Auth::id()]);
        $_SESSION['user']['name'] = $name;
        flash('success', 'Perfil actualizado.');
        redirect('cuenta');
    }

    private function passwordError(string $p, string $p2): ?string
    {
        if (strlen($p) < 8) return 'La contraseña debe tener al menos 8 caracteres.';
        if (!preg_match('/[A-Za-z]/', $p) || !preg_match('/\d/', $p)) return 'La contraseña debe combinar letras y números.';
        if ($p !== $p2) return 'La confirmación no coincide con la nueva contraseña.';
        return null;
    }

    public function changePassword(): void
    {
        $user = DB::one('SELECT password_hash FROM users WHERE id=?', [Auth::id()]);
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['password'] ?? '');
        if (!password_verify($current, $user['password_hash'])) {
            flash('danger', 'La contraseña actual no es correcta.');
            redirect('cuenta/password');
        }
        if ($new === $current) {
            flash('danger', 'La nueva contraseña debe ser distinta de la actual.');
            redirect('cuenta/password');
        }
        if ($err = $this->passwordError($new, (string)($_POST['password2'] ?? ''))) {
            flash('danger', $err);
            redirect('cuenta/password');
        }
        DB::exec('UPDATE users SET password_hash=?, must_change_password=0, updated_at=NOW() WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), Auth::id()]);
        Auth::clearMustChange();
        Activity::log('contrasena_cambiada', 'users', Auth::id());
        flash('success', 'Contraseña actualizada correctamente.');
        redirect(Auth::home());
    }
}
