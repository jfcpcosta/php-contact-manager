<?php namespace Core\Http;

class Request {

    public static function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function isAuthenticated() {
        $user = static::session('user');
        return $user ? $user : false;
    }

    public static function get(string $key): ?string {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function post(string $key): ?string {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
    
    public static function file(string $key): ?array {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function session(string $key, $value = null) {
        if (is_null($value)) {
            return Session::get($key);
        }

        Session::set($key, $value);
        return $value;
    }
    
    public static function cookie(string $key, string $value = null): ?string {

        if (is_null($value)) {
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        }

        setcookie($key, $value);
        return $value;
    }
}