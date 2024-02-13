<?php

require_once('./modules/Config.php');

class Router {
    public App $app;
    public \Twig\Environment $twig;
    public Config $config;
    private mixed $routes;
    private mixed $route;
    private mixed $controllerIsLoaded = false;
    public array $css = [];
    public array $js = [];
    private Logger $logger;

    public function __construct(App $app) {
        $this->app = $app;
        $this->twig = $app->twig;
        $this->config = $app->config;
        $this->routes = $this->config->get('ROUTES');

        $this->logger = $app->loggerManager->getLogger('Router');

        $this->css = [];
        $this->js = [];
    }

    public function findRoute() {
        for ($i = 0; $i < count($this->routes); $i++) {
            $this->routes[$i]['pattern'] = '/^(' . $this->routes[$i]['pattern'] . ')$/';

            if (
                strcasecmp($this->routes[$i]['method'], $_SERVER['REQUEST_METHOD']) === 0 &&
                preg_match($this->routes[$i]['pattern'], $_SERVER['REQUEST_URI'])
            ) {
                $this->route = $this->routes[$i];

                $this->logger->info('Route found: ' . $this->route['method'] . ' ' . $this->route['pattern']);

                return true;
            }
        }

        return false;
    }

    public function isStatic() {
        if (is_file($this->config->get('PUBLIC_PATH') . $_SERVER['REQUEST_URI'])) {
            return true;
        }
        if (is_file($this->config->get('PUBLIC_CACHE_PATH') . $_SERVER['REQUEST_URI'])) {
            return true;
        }

        return false;
    }

    public function loadController() {
        $controllerName = $this->route['controller'];
        $controllerPath = $this->config->get('CONTROLLER_PATH') . '/' . $controllerName . '.php';
        if (!is_file($controllerPath)) {
            return false;
        }
        
        require($controllerPath);

        if (!class_exists($controllerName)) {
            return false;
        }

        $this->route['controller'] = new $controllerName($this);

        $this->controllerIsLoaded = true;

        $this->logger->info('Controller loaded: ' . $controllerName);

        return true;
    }

    public function isControllerLoaded() {
        return $this->controllerIsLoaded;
    }

    public function render() {
        if (!$this->controllerIsLoaded) {
            if (!$this->loadController()) return false;
        }

        $this->logger->info('Rendering controller...');

        return $this->route['controller']->run();
    }

    public function render404() {
        $controllerPath = $this->config->get('CONTROLLER_PATH') . 'errors/404.php';
        if (!is_file($controllerPath)) {
            return false;
        }
        
        require_once($controllerPath);

        $controller = new Error404($this);

        $this->logger->info('Rendering 404 controller...');

        return $controller->run();
    }
}
