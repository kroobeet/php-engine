<?php

namespace App\Controllers\Auth;

use JetBrains\PhpStorm\NoReturn;
use PHPEngine\Router\Router;
use PHPEngine\Database\Session;
use PHPEngine\Controllers\BaseController;
use App\Models\User;

/**
 * Класс AuthController управляет логикой аутентификации пользователей,
 * включая вход, регистрацию и выход.
 */
class AuthController extends BaseController {
    /** @var Session $session Экземпляр класса Session для управления сессиями */
    private Session $session;

    /**
     * Конструктор класса AuthController
     *
     * @param Router $router Экземпляр маршрутизатора для обработки маршрутов
     * @param Session $session Экземпляр класса Session для управления сессиями
     */
    public function __construct(Router $router, Session $session)
    {
        parent::__construct($router);
        $this->session = $session;
        $this->session->start(); // Инициализация сессии
    }

    /**
     * Метод обработки страницы входа в систему (login)
     *
     * Обрабатывает GET-запросы для отображения формы входа и POST-запросы для аутентификации.
     * Если пользователь уже авторизован, перенаправляется на панель управления.
     * В случае успешной аутентификации, обновляет сессию и перенаправляет на панель управления.
     * @return void
     */
    public function login(): void {
        // Проверяем, авторизован ли пользователь
        if (isset($_SESSION['username'])) {
            header('Location: /dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->getUserByUsername($_POST['username']);

            // Проверяем правильность учетных данных
            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $this->session->updateSession($_SESSION['id']); // Обновляем идентификатор сессии
                header('Location: /dashboard'); // Перенаправляем на панель управления
                exit();
            } else {
                echo 'Invalid credentials'; // Сообщение об ошибке, если учетные данные неверны
            }
        }

        $this->render('login'); // Отображаем страницу логина
    }

    /**
     * Метод для обработки регистрации нового пользователя (register).
     *
     * Обрабатывает GET-запросы для отображения формы регистрации и POST-запросы для создания нового пользователя.
     * Выполняет валидацию введенных данных и проверяет, существует ли уже пользователь с таким username.
     * В случае успеха перенаправляет на страницу входа, иначе отображает сообщение об ошибке.
     *
     * @return void
     */
    public function register(): void
    {
        // Проверяем, авторизован ли пользователь
        if (isset($_SESSION['username'])) {
            header('Location: /dashboard'); // Перенаправляем на панель управления
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if (empty($username) || empty($password) || empty($confirmPassword)) {
                $error = 'All fields are required'; // Сообщение об обязательности всех полей
                $this->render('register', ['error' => $error]);
                return;
            }

            if ($password !== $confirmPassword) {
                $error = 'Passwords do not match'; // Ошибка несовпадения паролей
                $this->render('register', ['error' => $error]);
                return;
            }

            $userModel = new User();
            // Проверяем, существует ли уже пользователь с таким username
            if ($userModel->getUserByUsername($username)) {
                $error = 'Username already exists'; // Ошибка если пользователь уже существует
                $this->render('register', ['error' => $error]);
                return;
            }

            // Хэшируем пароль перед сохранением
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            if ($userModel->createUser($username, $hashedPassword)) {
                header('Location: /login'); // Перенаправляем на страницу входа после успешной регистрации.
                exit();
            } else {
                // Логирование ошибки создания пользователя
                error_log('User creation failed for username: ' . $username, destination: ERRORS_LOG_FILE);
                $error = 'Error during registration'; // Сообщение об ошибке регистрации
                $this->render('register', ['error' => $error]);
            }
        } else {
            $this->render('register'); // Отображаем форму регистрации
        }
    }


    /**
     * Метод для выхода пользователя из системы (logout).
     *
     * Очищает сессию и перенаправляет пользователя на страницу входа.
     *
     * @return void
     */
    #[NoReturn] public function logout(): void
    {
        $this->session->destroy(); // Уничтожаем сессию
        header('Location: /login'); // Перенаправляем на страницу входа
        exit();
    }
}