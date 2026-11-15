<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
    }

    public function loginStore(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password == '') {
            $this->flash('error', 'All fields required.');
            $this->redirect('/login');
        }

        $user = (new User())->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'Invalid credentials');
            $this->redirect('/login');
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
        $this->redirect('/dashboard');
    }

    public function login(): void
    {
        $this->view([
            'title' => 'Soma - Login',
            'content' => 'login'
        ]);
    }
}
