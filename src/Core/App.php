<?php namespace Core;

use Core\Error\NotFoundException;
use Core\Router\Route;
use Core\Router\Router;

class App {

    private $defaultRoute = '/';
    private $router;

    public function __construct() {
        $this->router = new Router();
    }

    public function start(): void {
        $uri = $this->getUri();
        try {
            $route = $this->router->get($uri);
            $this->invoke($route);
        } catch (NotFoundException $e) {
            $e->setHeader();
            die($e->getMessage());
        }
    }

    private function invoke(Route $route): void {
        $controller = $route->getController();
        $action = $route->getAction();

        if (class_exists($controller)) {
            $instance = new $controller();

            if (method_exists($controller, $action)) {
                call_user_func_array([$instance, $action], $route->getData());
                return;
            }
        }

        throw new NotFoundException("404 Not found");
    }

    private function getUri(): string {
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $this->defaultRoute;
    }
}