<?php

namespace PHPEngine\Router;

use PHPEngine\Database\Session;
use PHPEngine\Middleware\AuthMiddleware;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

class Router {
    /**
     * Класс Router управляет маршрутизацией запросов,
     * связывает URL с контроллерами и методами.
     * Поддерживает защиту маршрутов с использованием middleware
     * и автоматическое внедрение зависимостей в контроллеры.
     */

    /** @var array $routes Массив маршрутов с их обработчиками (контроллерами и методами) */
    private array $routes = [];
    /** @var array $middleware Массив middleware для маршрутов, требующих авторизации */
    private array $middleware = [];
    /** @var Session $session Объект для работы с сессиями */
    private Session $session;

    /**
     * Конструктор класса Router.
     *
     * @param Session $session Экземпляр класса Session для управления сессиями.
     */
    public function __construct(Session $session)
    {
        $this->session = $session; // Инициализируем сессию
    }

    /**
     * Добавляет маршрут в маршрутизатор
     *
     * @param string $url Путь URL, по которому должен срабатывать маршрут.
     * @param array $callback Массив, содержащий класс контроллера и его метод.
     * @param bool $requiresAuth Указывает, требует ли маршрут авторизации. По умолчанию - false
     * @return void
     */
    public function addRoute(string $url, array $callback, bool $requiresAuth = false): void
    {
        $this->routes[$url] = $callback;
        if ($requiresAuth) {
            $this->middleware[$url] = [new AuthMiddleware($this->session), 'handle'];
        }
    }

    /**
     * Запускает маршрутизатор, проверяя запрашиваемый URL
     * и передавая управление соответствующему контроллеру.
     *
     * @throws ReflectionException Если при рефлексии контроллеров или методов возникает ошибка.
     */
    public function run(): void
    {
        $url = $_SERVER['REQUEST_URI'];
        foreach ($this->routes as $route => $callback) {
            // Пробуем найти совпадение с параметрами
            $pattern = preg_replace('/{(\w+)}/', '([^/]+)', $route); // Заменяем параметры на регулярные выражения
            if (preg_match("#^$pattern$#", $url, $matches)) {
                // Проверка middleware
                if (isset($this->middleware[$route])) {
                    call_user_func($this->middleware[$route]);
                }
                // Передаем только те параметры, которые нужны контроллеру
                // Первое значение в $matches будет совпадением для самого URL, его можно игнорировать
                array_shift($matches); // Удаляем первый элемент массива
                $this->invoke($callback, $matches); // Передаем параметры в invoke
                return;
            }
        }
        // Если маршрут не найден, возвращаем 404
        http_response_code(404);
        echo "404 Not Found";
    }


    /**
     * Вызывает указанный метод контроллера с переданными параметрами.
     *
     * @param array $callback Массив с контроллером и методом.
     * @param array $params Параметры, передаваемые в метод контроллера.
     *
     * @throws ReflectionException Если при рефлексии методов возникает ошибка.
     */
    private function invoke(array $callback, array $params): void {
        [$controllerClass, $method] = $callback;

        // Создаем экземпляр контроллера с автоматическим внедрением зависимостей
        $controllerInstance = $this->resolveController($controllerClass);

        // Получаем параметры метода
        $reflection = new ReflectionMethod($controllerClass, $method);
        $reflectionParams = $reflection->getParameters();

        $resolvedParams = [];
        foreach ($reflectionParams as $index => $param) {
            $resolvedParam = $this->resolve($param, $params); // Передаем $params в resolve
            $resolvedParams[] = $resolvedParam;
        }

        // Вызываем метод контроллера с параметрами
        $controllerInstance->$method(...$resolvedParams);
    }

    /**
     * Разрешает параметр метода, основываясь на его типе и переданных значениях.
     *
     * @param ReflectionParameter $param Объект параметра метода, который нужно разрешить.
     * @param array $params Значения параметров, переданные в маршрут
     *
     * @return mixed Возвращает значение параметра (класс или примитив), либо null.
     */
    private function resolve(ReflectionParameter $param, array $params = []): mixed
    {
        // Проверяем, есть ли тип у параметра
        if ($param->getType() === null) {
            return null; // Если тип не задан, возвращаем null
        }

        $className = $param->getType()->getName();

        // Если параметр - это класс Router
        if ($className === Router::class) {
            return $this; // Возвращаем текущий экземпляр маршрутизатора
        }

        // Если параметр - это класс Session
        if ($className === Session::class) {
            return $this->session; // Возвращаем текущую сессию
        }

        // Проверяем наличие значения в параметрах
        $position = $param->getPosition();
        if (isset($params[$position])) {
            $value = $params[$position];

            // Если тип параметра - int, преобразуем значение
            if ($className === 'int') {
                return (int) $value; // Преобразуем в целое число
            }

            return $value; // Возвращаем значение из массива параметров
        }

        // Создаем новый экземпляр для других классов
        return new $className();
    }

    /**
     * Создает экземпляр контроллера с автоматическим внедрением зависимостей.
     *
     * @param string $controllerClass Имя класса контроллера.
     *
     * @return object Возвращаем созданный экземпляр контроллера.
     *
     * @throws ReflectionException Если при создании экземпляра возникает ошибка рефлексии.
     */
    private function resolveController(string $controllerClass): object
    {
        $reflection = new \ReflectionClass($controllerClass);
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            $dependencies = [];
            // Разрешаем зависимости конструктора
            foreach ($constructor->getParameters() as $param) {
                $resolvedParam = $this->resolve($param);
                $dependencies[] = $resolvedParam;
            }
            return $reflection->newInstanceArgs($dependencies); // Создаем экземпляр с зависимостями
        }

        // Если нет конструктора, создаем экземпляр без параметров
        return new $controllerClass();
    }

    /**
     * Возвращает строку URL для указанного маршрута.
     *
     * @param string $route Маршрут.
     *
     * @return string Возвращает URL для указанного маршрута.
     */
    public function getUrl(string $route): string
    {
        return $route; // Здесь можно добавить базовый URL, если нужно
    }
}
