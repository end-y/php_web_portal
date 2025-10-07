<?php

namespace PHPWebPortal\Controllers;

use Exception;
use PHPWebPortal\User;

class LoginController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }
    public function index()
    {
        return [
            'title' => 'Login',
            'showNav' => false,
            'isLoggedIn' => false,
        ];
    }
    public function login()
    {
        try {
        $this->user->login($_POST['username'], $_POST['password']);
        if($this->user->isTokenValid()) {
            header("Location: /");
            exit;
        }
        } catch (Exception $e) {
            return [
                'error' => "Login failed! Please check your credentials."
            ];
        }
    }
}

