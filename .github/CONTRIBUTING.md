<center><h1>Contribuer</h1></center>

> Ce document accompagne le [readme](README.md), le [code de conduite](CODE_OF_CONDUCT.md) ainsi que la [licence](LICENSE.md), merci d'en prendre connaissance.

## Sommaire

- [Sommaire](#sommaire)
- [Introduction](#introduction)
- [Exigence de développement](#exigence-de-développement)
  - [PHP](#php)
  - [HTML](#html)
  - [CSS](#css)
  - [Javascript](#javascript)
- [Architecture](#architecture)
- [Guide](#guide)
  - [Créer une page](#créer-une-page)
    - [Le contrôleur](#le-contrôleur)
    - [Fichier de configuration des routes](#fichier-de-configuration-des-routes)

## Introduction

Bienvenue ! Nous sommes ravis que vous souhaitiez contribuer à ce projet. Avant de commencer, veuillez prendre un moment pour lire ce guide afin de comprendre comment vous pouvez contribuer efficacement.

<!--t

## Soumettre un problème ou une idée (Issue)

Pour tout problème dans le projet ou idée d'amélioration, vous pouvez le signaler dans l'onglet `Issues` du projet du GitHub. Il y a cependant certaine convention à respecter lors du signalement :
- Dans le cas d'une idée :
  - Réaliser une description aussi précise que possible avec des exemples et des visuels si possible.
  - Le titre du signalement doit commencer par `[NEW] ` pour une idée d'ajout et `[UP] ` pour une idée de mise à jour.
- Dans le cas d'un problème :
  - Décrire précisément le problème observé
  - Donner les étapes permettant de reproduire le problème
  - Décrire votre environnement (système d'exploitation, version de PHP, version de Apache, ...)
  - Fournir des captures d'écran, logs, ... si possible
  - Le titre du signalement doit commencer par `[BUG] `

-->

## Exigence de développement

### PHP

Le code PHP doit respecter les règles _PHP Standards Recommendations_.

https://www.php-fig.org/psr

L'utilisation de la Programmation Orienté Objet est obligatoire sur PHP.

### HTML

Les documents HTML doivent respecter les conseils de W3C.

https://html.spec.whatwg.org/multipage

### CSS

Les documents CSS doivent respecter les conseils de W3C.

https://www.w3.org/TR/?filter-tr-name=&status%5B%5D=standard&tags%5B%5D=css

Utiliser normalize.css au lieu de réinitialiser des propriétés CSS ce qui peut ralentir l'application des styles.

https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.css

Utilisation de composants :

https://github.com/stubbornella/oocss/wiki#object-oriented-css

### Javascript

Le code Javascript doit respecter les conseils de StandardJS.

https://standardjs.com/rules

## Architecture

-   `.github` : Dossier contenant les fichiers spécifiques à GitHub
    -   `workflows` : Dossier contenant des instructions à effectuer pour certaines actions GIT (ex: push)
    -   `CODE_OF_CONDUCT.md` : Fichier contenant les règles de conduite à respecter sur le projet
    -   `CONTRIBUTING.md` : Ce fichier
    -   `ISSUE_TEMPLATE.md` : Fichier à destination des personnes voulant signaler un problème ou proposer une idée. Il sert de base au signalement.
-   `.vscode` : Dossier contenant les fichiers de configuration de Visual Studio Code pour le projet.
-   `cache` : Dossier contenant les fichiers générés par le moteur de templates et le gestionnaire d'assets (css, js).
    -   `public` : Dossier dédié au fichiers css et js.
    -   `twig` : Contient les fichiers générés par le moteur de templates Twig
-   `config` : Dossier de configuration
    -   `apache` : Contient un exemple de configuration des VHosts Apache
-   `logs` : Contient les fichiers de journalisation du site web et autre
    -   `apache` : Fichiers de journalisation de Apache
    -   `app` : Fichier journalisation du site web
-   `public` : Fichiers statiques
    -   `img` : Image du site (accessible depuis le VHosts `static.{{domain}}`)
    -   `fonts` : Fichiers de polices d'écriture
-   `src` : Dossier contenant le code source du projet
    -   `components` : Contient les composants du frontend
        -   `{{nom du composant}}` : Contient les fichiers d'un composant
            -   `{{nom du composant}}.css` : Styles du composant
            -   `{{nom du composant}}.js` : Code Javascript du composant
            -   `{{nom du composant}}.twig` : Template du composant
    -   `controllers` : Contient les classes des pages du site et endpoints de l'API
        -   `errors` : Dossier dédié aux pages d'erreur
    -   `modules`
    -   `static` : Assets JS et CSS. Ils seront par la suite combinés et placés dans le dossiers `cache/public`
    -   `views` : Contient les templates des pages ainsi que ceux de l'en-tête et du pied de page
        -   `errors` : Dossier dédié aux pages d'erreur
    -   `index.php` : Fichier PHP exécuté à chaque requête
-   `vendor` : Contient les librairies PHP comme le moteur de templates Twig
-   `.env` : Fichier de configuration de l'environnement d'exécution
-   `.gitignore`
-   `composer.json` : Liste de librairies PHP
-   `composer.lock` : Fichier généré par le gestionnaire de paquets : Composer
-   `README.md`

## Guide

### Créer une page

Pour créer une page, plusieurs fichiers sont nécessaire :

#### Le contrôleur
Le contrôleur `src/controllers/{{nom de la page avec une majuscule}}.php`

Ce fichier sert à créer la classe permettant de répondre à une requête. Elle doit porter le même nom que le fichier.

Exemple :

```php
<?php

require_once('../src/modules/controller.php');

class Home extends Controller {
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run() {
        return $this->render('home.twig', array(
            'config' => $this->config,
        ));
    }
};
```

Cette classe doit au minimum fournir une méthode `run`.

#### Fichier de configuration des routes

Ajouter une entrée dans le fichier de configuration `config/ROUTES.php` :
    
Exemple : 
```php
[
    'pattern' => '\/|\/home', // Home
    'controller' => 'Home', // Nom du contrôlleur
    'method' => 'GET',
]
```
Le `pattern` une partie d'une expression régulière. Elle sera par la suite entourée de `/^` et `$/`.
Exemple : `\/|\/home` => `/^\/|\/home$/`. Cette expression correspond aux chemins : `/` et `/home`.

