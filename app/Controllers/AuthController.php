<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function loginStore(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $token = $_POST['_token'] ?? null;
        if (!is_string($token) || !$this->validateToken($token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        if (
            !is_string($username) ||
            !is_string($password) ||
            $username === '' ||
            $password === ''
        ) {
            $this->flash('error', 'All fields required.');
            $this->redirect('/login');
        }

        $user = (new User())->findByUsername($username);

        if (
            !$user ||
            !isset($user['password']) ||
            !is_string($user['password']) ||
            !password_verify($password, $user['password'])
        ) {
            $this->flash('error', 'Invalid credentials');
            $this->redirect('/login');
        }

        unset($user['password']);
        if (!session_regenerate_id(true)) {
            http_response_code(500);
            exit('Unable to establish authenticated session.');
        }
        $_SESSION = [];
        $_SESSION['user'] = $user;
        $this->token();
        $this->redirect('/dashboard');
    }

    public function login(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $this->view([
            'title' => 'Soma - Login',
            'content' => 'login',
            'token' => $this->token(),
        ]);
    }

    public function logout(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        $token = $_POST['_token'] ?? null;
        if (!is_string($token) || !$this->validateToken($token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        if (!session_destroy()) {
            http_response_code(500);
            exit('Unable to end session.');
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            if (!setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ])) {
                http_response_code(500);
                exit('Unable to clear session cookie.');
            }
        }

        $this->redirect('/login');
    }
}
