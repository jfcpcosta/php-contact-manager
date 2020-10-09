<?php namespace Core\Error;

class NotFoundException extends \Exception {

    public function setHeader(): void {
        header("HTTP/1.1 404 Not found");
    }
}