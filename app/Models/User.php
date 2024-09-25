<?php

namespace App\Models;

use PHPEngine\Database\Database;
use PDOException;

/**
 * Класс User предоставляет методы для работы с пользователями в базе данных.
 * Управляет операциями получения, создания и поиска пользователей по идентификатору или имени пользователя
 */
class User {

    /** @var Database $db Экземпляр класса Database для выполнения запросов к базе данных */
    private Database $db;

    /** @var string $table Название таблицы в базе данных, используемой для хранения пользователей */
    protected string $table = 'users';

    /**
     * Конструктор класса User
     * Инициализирует подключение к базе данных через класс Database.
     */
    public function __construct() {
        $this->db = new Database(); // Инициализируем соединение с базой данных
    }

    /**
     * Получает список всех пользователей из базы данных.
     *
     * @return array|false Возвращает массив пользователей или false в случае ошибки.
     */
    public function getAllUsers(): false|array
    {
        return $this->db->query("SELECT * FROM $this->table"); // Запрос всех пользователей
    }

    /**
     * Получает информацию о пользователе по его username.
     *
     * @param string $username Имя пользователя для поиска.
     * @return array|false Возвращает массив данных пользователя или false, если пользователь не найден.
     */
    public function getUserByUsername(string $username): false|array
    {
        $sql = "SELECT * FROM $this->table WHERE username = :username LIMIT 1"; // Запрос пользователя по имени
        $params = ['username' => $username];
        $result = $this->db->query($sql, $params);
        return $result ? $result[0] : false; // Возвращаем первого найденного пользователя или false
    }

    /**
     * Создает нового пользователя в базе данных
     *
     * @param string $username Имя нового пользователя.
     * @param string $hashedPassword Хэшированный пароль нового пользователя.
     * @return bool Возвращает true при успешном создании пользователя, иначе false.
     */
    public function createUser(string $username, string $hashedPassword): bool
    {
        $sql = "INSERT INTO $this->table (username, password) VALUES (:username, :password)";
        $params = [
            'username' => $username,
            'password' => $hashedPassword
        ];

        try {
            $this->db->query($sql, $params); // Вставляем данные пользователя
            return true;
        } catch (PDOException $e) {
            error_log("User creation failed: " . $e->getMessage() . "\n", 3, ERRORS_LOG_FILE); // Логируем ошибку
            return false;
        }
    }

    /**
     * Получает информацию о пользователе по его идентификатору.
     *
     * @param int $id Идентификатор пользователя.
     * @return array|null|false Возвращает данные пользователя как массив,
     * null если пользователь не найден,
     * или false в случае ошибки.
     */
    public function getUserById(int $id): false|array|null
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id LIMIT 1"; // Запрос пользователя по ID
        $params = [':id' => $id];
        try {
            $result = $this->db->query($sql, $params); // Выполняем запрос
            return $result ? $result[0] : null; // Возвращаем данные пользователя или null
        } catch (PDOException $e) {
            error_log("User found failed: " . $e->getMessage() . "\n", 3, ERRORS_LOG_FILE); // Логируем ошибку
            return false; // Возвращаем false при ошибке
        }
    }




}
