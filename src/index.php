<?php

require '../vendor/autoload.php';

require_once('./modules/Router.php');
require_once('./modules/TwigUtils.php');
require_once('./modules/Config.php');
require_once('./modules/Logger.php');

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \Odan\Twig\TwigAssetsExtension;
use \Odan\Twig\TwigAssetsCache;

class App {
    public Config $config;
    public FilesystemLoader $loader;
    public Environment $twig;
    public Router $router;
    public LoggerManager $loggerManager;
    public Logger $logger;

    public function __construct($rawConfig, $env) {
        $this->config = new Config($rawConfig);

        $this->loggerManager = new LoggerManager($this->config);
        $this->logger = $this->loggerManager->getLogger('App');

        $this->logger->info('Starting application...');
        // $this->logger->info('=========================================================================');
        // $this->logger->info('=                          PROJET WEB GROUPE 2                          =');
        // $this->logger->info('=========================================================================');
        // $this->logger->info('= Environment: ' . $this->config->get('ENVIRONMENT'));
        // $this->logger->info('= PHP Version: ' . phpversion());
        // $this->logger->info('=========================================================================');


        $this->loader = new FilesystemLoader(array(
            $this->config->get('TEMPLATE_PATH'),
            $this->config->get('COMPONENTS_PATH'),
            $this->config->get('STATIC_PATH'),
        ));
        $this->twig = new Environment($this->loader, array(
            'cache' => $this->config->get('TWIG_CACHE_PATH'),
            'debug' => $this->config->get('ENVIRONMENT') === "development"
        ));

        // Add custom extensions
        $this->twig->addExtension(new TwigAssetsExtension(
            $this->twig,
            $this->config->get('TWIG_ASSETS_EXTENSION')
        ));

        // Add custom functions
        $this->twig->addExtension(new TwigUtils($this));

        // Create router
        $this->router = new Router($this);

        if ($this->config->get('ENVIRONMENT') === 'development') {
            $this->logger->info('Development mode enabled.');
            $this->clearAssets();
        }

        $this->applyHeaders();
        $this->processPath();
    }

    private function applyHeaders() {
        foreach ($this->config->get('HEADERS') as $key => $value) {
            header("$key: $value");
        }
    }

    private function processPath() {
        if ($this->router->findRoute()) {
            echo $this->router->render();
        } else echo $this->router->render404();
    }

    private function clearAssets() {
        $cache = new TwigAssetsCache($this->config->get('PUBLIC_CACHE_PATH'));
        $cache->clearCache();
        $this->removeDir($this->config->get('TWIG_CACHE_PATH'));
        $this->logger->info('Assets cache cleared.');
    }

    private function removeDir(string $dir): void {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator(
            $it,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }
};

$env = parse_ini_file('../.env');
$app = new App(require_once($env['CONFIG_PATH']), $env);

return true;
