<center><h1>Connecteur à la base de données</h1></center>

# Introduction

Le connecteur à la base de données est un module qui permet de se connecter à une base de données MySQL. Il est utilisé ensuite par les classes de modèles.

# Initialisation

Pour initialiser le connecteur, il suffit de créer une instance de la classe `Database` en lui passant les paramètres de connexion à la base de données.


```php
require_once 'src/modules/Database/Connector.php';

$database = new Database('localhost', 'ProjetWeb', 'root', 'TCqpZ4iJriGkJraT');
```

À partir du moment où une instance de la classe `Database` est créée, elle est utilisable pour effectuer des requêtes à la base de données. Elle est également récupérable avec la méthode statique `getInstance()`.

```php
$database = Database::getInstance();
```

# Utilisation 