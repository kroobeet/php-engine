<?php

namespace PHPEngine\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

/**
 * Класс для работы с базой данных, использующий PDO для выполнения запросов.
 * Загружает параметры соединения из переменных окружения и предоставляет методы
 * для выполнения SQL-запросов.
 */
class Database {
    /**
     * @var PDO $pdo Экземпляр PDO для взаимодействия с базой данных.
     */
    private PDO $pdo;

    /**
     * Конструктор, инициализирующий соединение с базой данных.
     * Загружает переменные окружения и устанавливает соединение с базой данных.
     *
     * @throws PDOException Если не удается установить соединение с базой данных.
     */
    public function __construct()
    {
        $this->loadEnv(); // Загружаем переменные окружения

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            // Устанавливаем соединение с базой данных
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Устанавливаем режим ошибок
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage()); // Обрабатываем ошибку соединения
        }
    }

    /**
     * Загружает переменные окружения из файла .env.
     *
     * @return void
     */
    private function loadEnv(): void
    {
        // Подключение к автозагрузчику Composer
        require_once BASE_DIR . '/vendor/autoload.php';

        // Загрузка переменных окружения
        $dotenv = Dotenv::createImmutable(BASE_DIR);
        $dotenv->load();
    }

    /**
     * Выполняет SQL-запрос и возвращает результат.
     *
     * @param string $sql SQL-запрос для выполнения.
     * @param array $params Параметры для запроса. По умолчанию пустой массив.
     *
     * @return false|array Возвращает массив ассоциативных массивов с результатами запроса
     *                     или false в случае ошибки.
     */
    public function query(string $sql, array $params = []): false|array
    {
        try {
            $stmt = $this->pdo->prepare($sql); // Подготовка SQL-запроса
            $stmt->execute($params); // Выполнение запроса с параметрами
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем все результаты запроса
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage(), destination: ERRORS_LOG_FILE); // Логирование ошибки выполнения запроса
            return []; // Возвращаем пустой массив при ошибке
        }
    }
}
