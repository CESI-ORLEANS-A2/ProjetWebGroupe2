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

## Méthodes statiques

### `getInstance()`

Cette méthode statique permet de récupérer l'instance de la classe `Database` qui a été créée.

Syntaxe :

```php
Database::getInstance(): Database
```

Exemple :

```php
$database = Database::getInstance();
```

### `isInitialized()`

Cette méthode statique permet de savoir si une instance de la classe `Database` a été créée.

Syntaxe :

```php
Database::isInitialized() : boolean
```

Exemple :

```php
if (!Database::isInitialized()) {
    echo 'Database not initialized';
}
```

## Méthodes d'instance

### `query`

Cette méthode permet d'exécuter une requête SQL sur la base de données. Elle prend en paramètre la requête SQL à exécuter et un tableau de paramètres à passer à la requête.

Syntaxe :

```php
$database->query(string $query, array $params = []): PDOStatement
```

Exemple :

```php
$statement = $database->query(
    'SELECT * FROM users WHERE id = :id',
    [':id' => 1]
);
$results = $statement->fetchAll();
```

### `fetch`

Cette méthode permet d'exécuter une requête SQL sur la base de données et de récupérer le premier résultat de la requête. Elle prend en paramètre la requête SQL à exécuter et un tableau de paramètres à passer à la requête.

Syntaxe :

```php
$database->fetch(string $query, array $params = []) : array
```

Exemple :

```php
$user = $database->fetch(
    'SELECT * FROM users WHERE id = :id',
    [':id' => 1]
);
```

### `fetchAll`

Cette méthode permet d'exécuter une requête SQL sur la base de données et de récupérer tous les résultats de la requête. Elle prend en paramètre la requête SQL à exécuter et un tableau de paramètres à passer à la requête.

Syntaxe :

```php
$database->fetchAll(string $query, array $params = []) : array
```

Exemple :

```php
$users = $database->fetchAll(
    'SELECT * FROM users'
);
```

### `fetchColumn`

Cette méthode permet d'exécuter une requête SQL sur la base de données et de récupérer la première colonne du premier résultat de la requête. Elle prend en paramètre la requête SQL à exécuter et un tableau de paramètres à passer à la requête.

Syntaxe :

```php
$database->fetchColumn(string $query, array $params = []) : mixed
```

Exemple :

```php
$count = $database->fetchColumn(
    'SELECT COUNT(*) FROM users'
);
```

### `insert`

Cette méthode permet d'insérer des données dans une table de la base de données. Elle prend en paramètre le nom de la table et un tableau de données à insérer.

Syntaxe :

```php
$database->insert(string $table, array $data) : void
```

Exemple :

```php
$database->insert(
    'users',
    [
        'username' => 'john_doe',
        'email' => 'john.doe@mail.xyz'
    ]
);
```

### `update`

Cette méthode permet de mettre à jour des données dans une table de la base de données. Elle prend en paramètre le nom de la table, un tableau de données à mettre à jour et un tableau de conditions.

Syntaxe :

```php
$database->update(
    string $table,
    array $data,
    string $whereString,
    array params = []
) : void
```

Exemple :

```php
$database->update(
    'users',
    [
        'username' => 'jane_doe',
        'email' => 'jane.doe@mail.xyz'
    ],
    'id = :id',
    [':id' => 1]
);
```

### `delete`

Cette méthode permet de supprimer des données dans une table de la base de données. Elle prend en paramètre le nom de la table et un tableau de conditions.

Syntaxe :

```php
$database->delete(
    string $table,
    string $whereString,
    array params = []
) : void
```

Exemple :

```php
$database->delete(
    'users',
    'id = :id',
    [':id' => 1]
);
```

### `lastInsertId`

Cette méthode permet de récupérer l'identifiant de la dernière ligne insérée dans une table de la base de données.

Syntaxe :

```php
$database->lastInsertId() : int
```

Exemple :

```php
$id = $database->lastInsertId();
```

### `count`

Cette méthode permet de compter le nombre de lignes d'une table de la base de données. Elle prend en paramètre le nom de la table et un tableau de conditions.

Syntaxe :

```php
$database->count(
    string $table,
    string $whereString = '',
    array params = []
) : int
```

Exemple :

```php
$count = $database->count(
    'users',
    'role = :role',
    [':role' => 'admin']
);
```
