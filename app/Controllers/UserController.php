<?php

namespace App\Controllers;

use PHPEngine\Database\Session;
use PHPEngine\Router\Router;
use PHPEngine\Controllers\BaseController;
use App\Models\User;

/**
 * Класс UserController управляет действиями, связанными с пользователями,
 * такими как отображение информации о пользователе.
 */
class UserController extends BaseController {

    /** @var Session $session Экземпляр класса Session для управления сессиями */
    private Session $session;

    /**
     * Конструктор класса UserController
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
     * Метод для отображения информации о пользователе.
     *
     * Получает данные пользователя по его идентификатору и передает их для рендеринга страницы пользователя.
     *
     * @param int $id Идентификатор пользователя, чья информация будет отображена.
     * @return void
     */
    public function show(int $id): void
    {
        $user = new User(); // Создаем экземпляр модели User
        $data = ['user' => $user->getUserById($id)]; // Получаем данные пользователя по ID
        $this->render('user', $data); // Отправляем данные на рендеринг страницы пользователя
    }
}
