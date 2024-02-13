<?php

class TwigUtils extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface {
    protected $twig;
    protected $config;

    protected $css = [];
    protected $js = [];
    protected $name = 'base';

    private Logger $logger;

    public function __construct(App $app) {
        $this->twig = $app->twig;
        $this->config = $app->config;
        $this->logger = $app->loggerManager->getLogger('TwigUtils');
    }
    public function getFunctions() {
        return  [
            new \Twig\TwigFunction('addCSS', [$this, 'addCSS']),
            new \Twig\TwigFunction('addJS', [$this, 'addJS']),
            new \Twig\TwigFunction('getCSS', [$this, 'getCSS']),
            new \Twig\TwigFunction('getJS', [$this, 'getJS']),
            new \Twig\TwigFunction('setName', [$this, 'setTemplateName']),
            new \Twig\TwigFunction('getName', [$this, 'getTemplateName']),
        ];
    }

    public function getGlobals(): array {
        return [
            'config' => $this->config,
            'twig' => $this->twig,
        ];
    }

    public function getName() {
        return 'twig_utils';
    }

    public function addCSS(string|array $value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                if (!in_array($v, $this->css))
                    array_push($this->css, $v);
            }
        } else if (is_string($value)) {
            if (!in_array($value, $this->css))
                array_push($this->css, $value);
        }
    }

    public function addJS(string|array $value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                if (!in_array($v, $this->js))
                    array_push($this->js, $v);
            }
        } else if (is_string($value)) {
            if (!in_array($value, $this->js))
                array_push($this->js, $value);
        }
    }

    public function getCSS() {
        return $this->css;
    }

    public function getJS() {
        return $this->js;
    }

    public function setTemplateName(string $name) {
        $this->name = $name;
    }

    public function getTemplateName() {
        return $this->name;
    }
}
