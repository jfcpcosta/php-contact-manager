<?php namespace Core\Router;

use Core\Error\NotFoundException;
use Core\Persistence\File;

class Router {

    const ROUTES_PATH = '../config/routes.config.php';

    private $routes;

    public function __construct() {
        if (File::exists(self::ROUTES_PATH)) {
            $this->routes = require self::ROUTES_PATH;
        } else {
            $this->routes = [];
        }
    }

    public function add(string $endpoint, Route $route): void {
        if (!isset($this->routes[$endpoint])) {
            $this->routes[$endpoint] = sprintf("%s::%s", $route->getController(), $route->getAction());
        }
    }

    public function get($uri): Route {
        if (strpos($uri, ':') === false && isset($this->routes[$uri])) {
            return new Route($this->routes[$uri]);
        } else {
            $segments = explode('/', $uri);
            $segmentsCount = count($segments);
            $routeData = [];

            foreach ($this->routes as $key => $route) {
                $routeSegments = explode('/', $key);

                if ($segmentsCount === count($routeSegments)) {
                    $match = true;

                    for ($i = 0; $i < $segmentsCount; $i++) {
                        if (isset($routeSegments[$i][0]) && $routeSegments[$i][0] === ':') {
                            $routeData[substr($routeSegments[$i], 1)] = $segments[$i];
                            continue;
                        }

                        if ($segments[$i] !== $routeSegments[$i]) {
                            $match = false;
                            break;
                        }
                    }

                    if ($match) {
                        return new Route($route, $routeData);
                    }
                }
            }
        }

        throw new NotFoundException("404 Not Found");
    }
}