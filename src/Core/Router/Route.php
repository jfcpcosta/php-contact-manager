<?php namespace Core\Router;

class Route {
    
    private $controller;
    private $action;
    private $data;

    public function __construct($route, $data = []) {
        $this->parse($route);
        $this->data = $data;
    }

    private function parse($route) {
        $parts = explode('::', $route);
        $this->controller = $parts[0];
        $this->action = $parts[1];
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function hasData() {
        return count($this->data) > 0;
    }

    public function getData() {
        return $this->data;
    }
}