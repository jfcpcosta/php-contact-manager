<?php namespace Core\Http;

class Session {

    public static function start(): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get(string $key) {
        static::start();
        return isset($_SESSION[$key]) ? unserialize($_SESSION[$key]) : null;
    } 

    public static function set(string $key, $value): void {
        static::start();
        $_SESSION[$key] = serialize($value);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function id(): string {
        return session_id();
    }

    public static function destroy(): void {
        static::start();

        session_unset();
        session_destroy();
    }
}