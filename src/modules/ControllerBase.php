<?php

class ControllerBase {
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

    protected function reply($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'body' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS);
    }
};
