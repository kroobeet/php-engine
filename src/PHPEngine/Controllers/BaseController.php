<?php

namespace PHPEngine\Controllers;

use PHPEngine\Router\Router;

/**
 * Базовый контроллер, от которого наследуются все контроллеры приложения.
 * Предоставляет базовую функциональность для работы с маршрутизатором и рендеринга представлений.
 */
class BaseController {
    /**
     * @var Router $router Экземпляр маршрутизатора, используется для управления маршрутами и генерации ссылок.
     */
    protected Router $router;

    /**
     * Конструктор контроллера.
     *
     * @param Router $router Экземпляр маршрутизатора, автоматически внедряется в контроллеры.
     */
    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * Рендерит представление и передаёт данные в шаблон.
     *
     * @param string $view Имя представления (без расширения .php), которое нужно отобразить.
     * @param array $data Массив данных, которые будут доступны в шаблоне.
     *                    Данные извлекаются в переменные и доступны в представлении.
     *
     * @return void
     */
    public function render(string $view, array $data = []): void {
        // Подключаем объект маршрутизатора для использования в шаблонах
        $data['router'] = $this->router;

        // Формируем путь к файлу представления
        $viewPath = BASE_DIR . "/views/{$view}.php";

        // Проверяем наличие файла представления и отображаем его
        if (file_exists($viewPath)) {
            extract($data); // Извлекаем данные в переменные
            include $viewPath; // Включаем файл представления
        } else {
            // Выводим сообщение об ошибке, если представление не найдено
            echo "View not found: {$viewPath}";
        }
    }
}
