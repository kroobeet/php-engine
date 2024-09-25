<?php

namespace PHPEngine\Database;

/**
 * Класс для управления сессиями, который хранит данные сессии в базе данных.
 * Позволяет создавать, обновлять и удалять сессии, а также проверять статус авторизации.
 */
class Session
{
    /**
     * @var Database $db Экземпляр класса Database для работы с базой данных.
     */
    private Database $db;

    /**
     * @var array $data Хранит данные текущей сессии.
     */
    private array $data = []; // Инициализация $data

    /**
     * Конструктор, инициализирующий соединение с базой данных.
     */
    public function __construct()
    {
        $this->db = new Database(); // Создаем новый экземпляр Database
    }

    /**
     * Запускает сессию и инициализирует данные сессии.
     *
     * Если сессия уже активна, просто обновляет данные.
     * Также удаляет старые сессии.
     *
     * @return void
     */
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(); // Стартуем сессию, только если она еще не активна
        }

        $this->gc(); // Удаление старых сессий

        if (isset($_SESSION['id'])) {
            $sessionData = $this->getSessionData($_SESSION['id']);
            if ($sessionData) {
                $_SESSION = json_decode($sessionData['data'], true);
                $this->data = $_SESSION; // Заполняем $data из $_SESSION
                $this->updateSession($_SESSION['id']);
            } else {
                session_destroy(); // Уничтожаем сессию, если данные не найдены
            }
        } else {
            $_SESSION['id'] = session_id();
            $this->createSession($_SESSION['id']); // Создаем новую сессию
        }
    }

    /**
     * Создает новую сессию в базе данных.
     *
     * @param string $id Идентификатор сессии.
     * @return void
     */
    private function createSession(string $id): void
    {
        $data = json_encode($_SESSION);
        $lastActivity = time();
        $this->db->query("INSERT INTO sessions (id, data, last_activity) VALUES (?, ?, ?)", [$id, $data, $lastActivity]);
    }

    /**
     * Обновляет данные существующей сессии в базе данных.
     *
     * @param string $id Идентификатор сессии.
     * @return void
     */
    public function updateSession(string $id): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $data = json_encode($_SESSION);
            $lastActivity = time();
            $this->db->query("UPDATE sessions SET data = ?, last_activity = ? WHERE id = ?", [$data, $lastActivity, $id]);
        }
    }

    /**
     * Получает данные сессии из базы данных по идентификатору.
     *
     * @param string $id Идентификатор сессии.
     * @return array|null Данные сессии или null, если сессия не найдена.
     */
    private function getSessionData(string $id): ?array
    {
        $result = $this->db->query("SELECT * FROM sessions WHERE id = ?", [$id]);
        return $result ? $result[0] : null; // Возвращаем данные сессии или null
    }

    /**
     * Удаляет старые сессии из базы данных.
     *
     * Сессии удаляются, если их время последней активности превышает заданный лимит.
     *
     * @return void
     */
    public function gc(): void
    {
        $lifetime = 3600; // Время жизни сессии в секундах
        $this->db->query("DELETE FROM sessions WHERE last_activity < ?", [time() - $lifetime]); // Удаление старых сессий
    }

    /**
     * Проверяет, авторизован ли пользователь.
     *
     * @return bool true, если пользователь авторизован, иначе false.
     */
    public function isLoggedIn(): bool
    {
        return isset($this->data['username']); // Проверяем наличие имени пользователя в данных сессии
    }

    /**
     * Получает значение по ключу из данных сессии.
     *
     * @param string $key Ключ для получения значения.
     * @return mixed|null Возвращает значение по ключу или null, если ключ не найден.
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null; // Возвращает значение по ключу
    }

    /**
     * Устанавливает значение по ключу в данные сессии.
     *
     * @param string $key Ключ, по которому будет установлено значение.
     * @param mixed $value Значение, которое будет установлено.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value; // Устанавливает значение по ключу
        $_SESSION[$key] = $value; // Также обновляем $_SESSION
    }

    /**
     * Уничтожает сессию и очищает данные.
     *
     * @return void
     */
    public function destroy(): void
    {
        $_SESSION = []; // Очищаем данные сессии

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy(); // Уничтожаем сессию на сервере
        }

        setcookie(session_name(), '', time() - 3600, '/'); // Удаление куки
    }
}
