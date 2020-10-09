<?php namespace ContactManager\Controllers;

use ContactManager\Models\Repositories\UsersRepository;
use Core\Http\Request;
use Core\Mvc\Controller;

class AuthController extends Controller {

    private $users;

    public function __construct() {
        $this->users = new UsersRepository();
    }

    public function login(): void {
        if ($this->isPost()) {
            if ($this->users->attemptLogin(Request::post('username'), Request::post('password'))) {
                $this->redirect('/contacts');
            }

            $message = 'Bad credentials';
        }

        $this->render('login', [
            'message' => isset($message) ? $message : Request::get('message')
        ]);
    }
    
    public function logout(): void {
        $this->users->doLogout();
        $this->redirect('/');
    }
    
    public function signUp(): void {
        if ($this->isPost()) {
            $username = Request::post('username');
            $password = Request::post('password');
            $name = Request::post('name');

            if ($this->users->signUpUser($username, $password, $name)) {
                $this->redirect('/');
            }

            $message = 'Error processing sign up!';
        }

        $this->render('signup', [
            'message' => isset($message) ? $message : null
        ]);
    }
}