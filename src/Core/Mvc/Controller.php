<?php namespace Core\Mvc;

use Core\Http\Request;
use Core\Http\Response;

abstract class Controller {

    public function render(string $name, array $data = null, bool $templated = true) {
        if ($templated) {
            View::renderWithTemplate($name, $data);
        } else {
            View::render($name, $data);
        }
    }

    protected function redirect($url) {
        Response::redirect($url);
    }

    protected function isPost() {
        return Request::isPost();
    }
}