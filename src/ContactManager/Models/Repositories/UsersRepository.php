<?php namespace ContactManager\Models\Repositories;

use Core\Http\Request;
use Core\Http\Session;
use Core\Mvc\Model;

class UsersRepository extends Model {

    const TABLE = 'users';

    public function getUserByUsername(string $username): ?object {
        $users = $this->database->where(self::TABLE, [
            'username' => $username
        ]);
        return isset($users[0]) ? $users[0] : null;
    }

    public function attemptLogin(string $username, string $password): bool {
        $user = $this->getUserByUsername($username);

        if (!is_null($user) && hash('sha256', $password) === $user->password) {

            Request::session('auth', true);
            Request::session('user', $user);

            // Logger::info("user $username logged in");

            return true;
        }

        return false;
    }

    public function doLogout(): void {
        $user = Request::session('user');
        // Logger::info("user $user->username logged out");
        
        Session::remove('auth');
        Session::remove('user');
    }

    public function isAuthenticated(): bool {
        return Request::session('auth');
    }

    public function signUpUser(string $username, string $password, string $name): bool {
        $password = hash('sha256', $password);
        $res = $this->database->insert(self::TABLE, [
            'username' => $username,
            'password' => $password, 
            'name' => $name
        ]);

        return $res['stmt']->rowCount() > 0;
    }
}