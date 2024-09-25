<?php

/**
 * Файл маршрутизации приложения.
 * Здесь определяются маршруты и их соответствующие контроллеры и методы.
 * Также настраиваются маршруты, которые требуют аутентификации.
 */

use PHPEngine\Database\Session;
use PHPEngine\Router\Router;

use App\Controllers\Auth\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\UserController;

// Инициализация маршрутизатора с сессией
$router = new Router(new Session());

/**
 * Определение маршрутов:
 * - '/' — Главная страница (без авторизации)
 * - '/dashboard' — Панель управления (требуется авторизация)
 * - '/login' — Страница входа (без авторизации)
 * - '/register' — Страница регистрации (без авторизации)
 * - '/logout' — Выход из аккаунта (требуется авторизация)
 * - '/user/{id}' — Страница пользователя по ID (требуется авторизация)
 */
$router->addRoute('/', [HomeController::class, 'index'], false);
$router->addRoute('/dashboard', [DashboardController::class, 'index'], true);
$router->addRoute('/login', [AuthController::class, 'login'], false);
$router->addRoute('/register', [AuthController::class, 'register'], false);
$router->addRoute('/logout', [AuthController::class, 'logout'], true);
$router->addRoute('/user/{id}', [UserController::class, 'show'], true);

try {
    // Запуск маршрутизатора для обработки запроса
    $router->run();
} catch (ReflectionException $e) {
    // Логирование ошибки рефлексии
    error_log("Reflection error: " . $e->getMessage(), destination: ERRORS_LOG_FILE);
    return;
}
