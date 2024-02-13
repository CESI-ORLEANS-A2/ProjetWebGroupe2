<?php

class Controller {
    protected App $app;
    protected Router $router;
    protected \Twig\Environment $twig;
    protected mixed $config;
    protected Logger $logger;

    protected function __construct(Router $router) {
        $this->app = $router->app;
        $this->router = $router;
        $this->twig = $router->twig;
        $this->config = $router->config;

        $this->logger = $this->app->loggerManager->getLogger('Controller');
    }

    protected function render($template, $data = array()) {

        $this->logger->info('Rendering template: ' . $template . ' with data: ' . json_encode($data));

        echo $this->twig->render($template, [
            'router' => $this->router,
        ] + $data);
    }
};
