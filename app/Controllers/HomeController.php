<?php

namespace App\Controllers;

use PHPEngine\Database\Session;
use PHPEngine\Router\Router;
use PHPEngine\Controllers\BaseController;

/**
 * Класса HomeController отвечает за управление отображением главной страницы приложения.
 */
class HomeController extends BaseController {

    /** @var Session $session Экземпляр класса Session для управления сессией пользователя */
    private Session $session;

    /**
     * Конструктор класса HomeController.
     *
     * @param Router $router Экземпляр маршрутизатора для обработки маршрутов.
     * @param Session $session Экземпляр класса Session для управления сессиями.
     */
    public function __construct(Router $router, Session $session)
    {
        parent::__construct($router);
        $this->session = $session;
        $this->session->start(); // Инициализируем сессию
    }

    /**
     * Метод для отображения главной страницы.
     *
     * Формирует данные для отображения, включая заголовок страницы и статус авторизации пользователя.
     * Отправляет эти данные для рендеринга главной страницы.
     *
     * @return void
     */
    public function index(): void
    {
        $data = [
            'title' => 'Home', // Заголовок страницы
            'session' => $this->session->isLoggedIn() // Проверка статуса авторизации пользователя для использования в шаблоне
        ];
        $this->render('home', $data); // Отображаем главную страницу с данными
    }
}
