<?php

// Chargement des classes
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \Odan\Twig\TwigAssetsExtension;
use \Odan\Twig\TwigAssetsCache;

// Chargement des dépendances
require '../vendor/autoload.php';

// Chargement des librairies
require_once ('./modules/Router.php');
require_once ('./modules/TwigUtils.php');
require_once ('./modules/Config.php');
require_once ('./modules/Logger.php');

require_once ('./modules/Database/Connector.php');
require_once ('./modules/Database/Models/Account.php');
require_once ('./modules/Database/Models/Session.php');

/**
 * Classe App représente l'application principale.
 */
class App
{
    /**
     * @var Config $config Configuration de l'application.
     */
    public Config $config;

    /**
     * @var FilesystemLoader $loader Chargeur de fichiers pour Twig.
     */
    public FilesystemLoader $loader;

    /**
     * @var Environment $twig Environnement Twig.
     */
    public Environment $twig;

    /**
     * @var Router $router Routeur de l'application.
     */
    public Router $router;

    /**
     * @var LoggerManager $loggerManager Gestionnaire de loggers.
     */
    public LoggerManager $loggerManager;

    /**
     * @var Logger $logger Logger de l'application.
     */
    public Logger $logger;

    public Session|null $session = null;
    public Account|null $connectedAccount = null;

    /**
     * Constructeur de la classe App.
     *
     * @param mixed $rawConfig Configuration brute de l'application.
     * @param string $env Environnement de l'application.
     */
    public function __construct($rawConfig, $env)
    {
        $this->config = new Config($rawConfig);

        $this->loggerManager = new LoggerManager($this->config);
        $this->logger = $this->loggerManager->getLogger('App');

        $this->logger->info('Starting application...');

        $this->loader = new FilesystemLoader(
            array(
                $this->config->get('TEMPLATE_PATH'),
                $this->config->get('COMPONENTS_PATH'),
                $this->config->get('STATIC_PATH'),
            )
        );
        $this->twig = new Environment(
            $this->loader,
            array(
                'cache' => $this->config->get('ENVIRONMENT') === "production" ?? $this->config->get('TWIG_CACHE_PATH'),
                'debug' => $this->config->get('ENVIRONMENT') === "development"
            )
        );

        // Add custom extensions
        $this->twig->addExtension(
            new TwigAssetsExtension(
                $this->twig,
                $this->config->get('TWIG_ASSETS_EXTENSION')
            )
        );

        // Add custom functions
        $this->twig->addExtension(new TwigUtils($this));

        // Create router
        $this->router = new Router($this);

        // Clear assets cache in development mode
        if (
            $this->config->get('ENVIRONMENT') === 'development'
            && strpos($_SERVER['REQUEST_URI'], 'api/') === false
        ) {
            $this->logger->info('Development mode enabled.');
            $this->clearAssets();
        }

        $this->applyHeaders();

        $this->getConnectedUser();

        $this->processPath();
    }

    /**
     * Récupère l'utilisateur connecté.
     */
    private function getConnectedUser()
    {
        new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS')
        );

        if (isset($_COOKIE['token'])) {
            $session = Session::getByToken($_COOKIE['token']);
            if ($session) {
                $user = Account::getById($session->get('ID_Account'));
                if ($user) {
                    $this->connectedAccount = $user;
                    $this->session = $session;
                }
            }
        }
    }

    /**
     * Applique les en-têtes définis dans la configuration.
     */
    private function applyHeaders()
    {
        foreach ($this->config->get('HEADERS') as $key => $value) {
            header("$key: $value");
        }
    }

    /**
     * Traite le chemin de la requête et exécute la route correspondante.
     */
    private function processPath()
    {
        if ($this->router->findRoute()) {
            echo $this->router->run();
        } else
            echo $this->router->render404();
    }

    /**
     * Efface le cache des assets en mode développement.
     */
    private function clearAssets()
    {
        $cache = new TwigAssetsCache($this->config->get('PUBLIC_CACHE_PATH'));
        $cache->clearCache();
        $this->removeDir($this->config->get('TWIG_CACHE_PATH'));
        $this->logger->info('Assets cache cleared.');
    }

    /**
     * Supprime un répertoire et son contenu récursivement.
     *
     * @param string $dir Chemin du répertoire à supprimer.
     */
    private function removeDir(string $dir): void
    {
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
}
;

// Chargement des variables d'environnement
$env = parse_ini_file('../.env');

try {
    // Création de l'application
    $app = new App(require_once ($env['CONFIG_PATH']), $env);

    return true;
} catch (Exception $e) {
    if ($env['ENVIRONMENT'] === 'development') {
        echo '⚠️ Une erreur est survenue : </br>';
        echo $e->getMessage();
        echo '</br>';
        echo str_replace('#', '</br>#', $e->getTraceAsString());
    } else {
        echo '⚠️ Une erreur est survenue : </br>';
        echo $e->getMessage();
    }
    return false;
}
