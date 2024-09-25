<?php

namespace PHPEngine\Middleware;

use PHPEngine\Database\Session;

class AuthMiddleware
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    public function handle(): void
    {
        // Логика проверки авторизации
        if (!$this->session->isLoggedIn() !== null || !$this->session->isLoggedIn())
        {
            http_response_code(403);
            echo "403 Forbidden: You must be logged in to access this page.";
            exit();
        }
    }
}