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
                in_array($_SERVER['REQUEST_METHOD'], $this->routes[$i]['methods']) &&
                preg_match(strtolower($this->routes[$i]['pattern']), strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), $matches) &&
                (!isset($this->routes[$i]['environment']) || $this->routes[$i]['environment'] === $this->config->get('ENVIRONMENT'))
            ) {
                $this->route = $this->routes[$i];

                $this->logger->info('Route found: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $this->route['pattern']);

                return true;
            }
        }

        return false;
    }

    public function loadController() {
        $controllerPath =
            $this->config->get('CONTROLLER_PATH')
            . $this->route['controller'];

        if (!is_file($controllerPath)) {
            return false;
        }

        require($controllerPath);

        if (!class_exists('Controller')) {
            return false;
        }

        $this->route['controller'] = new Controller($this);

        $this->controllerIsLoaded = true;

        $this->logger->info('Controller loaded: ' . $controllerPath);

        return true;
    }

    public function isControllerLoaded() {
        return $this->controllerIsLoaded;
    }

    public function run() {
        if (!$this->controllerIsLoaded) {
            if (!$this->loadController()) return false;
        }

        $this->logger->info('Running controller...');

        return $this->route['controller']->run();
    }

    public function render404() {
        $controllerPath = $this->config->get('CONTROLLER_PATH') . 'errors/404.php';
        if (!is_file($controllerPath)) {
            return false;
        }

        require_once($controllerPath);

        $controller = new Error404Controller($this);

        $this->logger->info('Rendering 404 controller...');

        return $controller->run();
    }
}
