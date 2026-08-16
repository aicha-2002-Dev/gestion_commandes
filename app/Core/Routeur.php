<?php

class Routeur
{
    private array $routes = [
        '/' => [
            'controller' => 'POSController',
            'action'     => 'afficherCaisse',
        ]
       
    ];

  
    public function distribuer(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $route = $this->routes[$uri] ?? $this->routes['/'];

        $controller = $route['controller'];
        $action     = $route['action'];

        $cheminFichier = __DIR__ . '/../Controller/' . $controller . '.php';
    //     var_dump($cheminFichier);
    // var_dump(file_exists($cheminFichier));
    // die;
        if (file_exists($cheminFichier)) {
            require_once $cheminFichier;

            $instance = new $controller();
    

            if (method_exists($instance, $action)) {
                $instance->$action();
                return;
            }
        }

        http_response_code(404);
        echo "Page not found";
    }
}