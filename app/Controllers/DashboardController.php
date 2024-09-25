<?php

namespace App\Controllers;

use PHPEngine\Database\Session;
use PHPEngine\Router\Router;
use PHPEngine\Controllers\BaseController;

/**
 * Класс DashboardController управляет отображением панели управления для авторизованных пользователей
 */
class DashboardController extends BaseController {
    /** @var Session $session Экземпляр класса Session для управления сессиями */
    private Session $session;

    /**
     * Конструктор класса DashboardController.
     *
     * @param Router $router Экземпляр маршрутизатора для обработки маршрутов.
     * @param Session $session Экземпляр класса Session для управления сессиями.
     */
    public function __construct(Router $router, Session $session) // Автоматически внедряем Router и Session
    {
        parent::__construct($router);
        $this->session = $session;
        $this->session->start(); // Инициализируем сессию
    }

    /**
     * Метод для отображения главной страницы панели управления.
     *
     * Проверяет, авторизован ли пользователь, с помощью сессии.
     * Если пользователь авторизован, отображает панель управления с именем пользователя.
     * Если пользователь не авторизован, перенаправляет на страницу входа.
     *
     * @return void
     */
    public function index(): void
    {
        // Проверяем, авторизован ли пользователь
        if ($this->session->isLoggedIn()) {
            $data = [
                'title' => 'Dashboard', // Заголовок страницы
                'username' => $this->session->get('username') // Получаем имя пользователя из сессии
            ];
            $this->render('dashboard', $data); // Отображаем страницу панели управления с данными
        } else {
            // Если пользователь не авторизован, перенаправляем на страницу входа
            header('Location: /login');
            exit();
        }
    }
}
