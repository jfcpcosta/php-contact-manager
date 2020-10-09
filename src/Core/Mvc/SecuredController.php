<?php namespace Core\Mvc;

use Core\Http\FlashBag;
use Core\Http\Request;

abstract class SecuredController extends Controller {

    protected $user;

    public function __construct() {
        $this->user = Request::isAuthenticated();

        if (!$this->user) {
            FlashBag::add('Forbidden', 'danger');
            $this->redirect('/');
        }
    }
}