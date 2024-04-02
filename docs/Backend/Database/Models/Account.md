<center><h1>Modèle de compte</h1></center>

# Introduction

Le modèle de compte est une classe qui permet de manipuler les comptes d'utilisateurs en base de données. Il est utilisé par le contrôleur de compte pour effectuer des opérations sur les comptes.

# Initialisation

Pour pouvoir utiliser le modèle de compte, il suffit de l'inclure dans le fichier où il est utilisé.

```php
require_once 'src/modules/Database/Models/Account.php';
```	

> **Note** : Le modèle de compte nécessite que le connecteur à la base de données soit initialisé. Une fois initialisé, le modèle de compte peut être utilisé pour effectuer des opérations sur les comptes d'utilisateurs dans la base de données.
>
> ```php
> require_once 'src/modules/Database/Connector.php';
> 
> new Database('localhost', 'ProjetWeb', 'root', 'TCqpZ4iJriGkJraT');
> ```

# Utilisation

## Constructeur

Le constructeur du modèle de compte prend en paramètre la liste des propriétés du compte. Ces propriétés sont ensuite utilisées pour initialiser les attributs de l'objet.

```php
$account = new Account(
    int ID,
    DateTime CreationDate,
    string Username,
    string Password,
    string Type,
    int Class
)
```

Le constructeur du modèle de compte peut être utilisé pour créer un nouvel objet de compte, cette instance peut ensuite être ajoutée à la base de données.

## Méthodes statiques

### getByID

La méthode statique `getByID` permet de récupérer un compte à partir de son identifiant unique.

Syntaxe :
```php
Account::getByID(int $id): Account
```

Exemple :
```php
$account = Account::getByID(1);
```

### getByUsername

La méthode statique `getByUsername` permet de récupérer un compte à partir de son nom d'utilisateur.

Syntaxe :
```php
Account::getByUsername(string $username): Account
```

Exemple :
```php
$account = Account::getByUsername('admin');
```

### getAll

La méthode statique `getAll` permet de récupérer tous les comptes de la base de données.

Syntaxe :
```php
Account::getAll(): array
```

Exemple :
```php
$accounts = Account::getAll();
```

### fromArray

La méthode statique `fromArray` permet de créer un compte à partir d'un tableau associatif.

Syntaxe :
```php
Account::fromArray(array $data): Account
```

Exemple :
```php
$account = Account::fromArray([
    'ID' => 1,
    'CreationDate' => '2021-01-01
    'Username' => 'admin',
    'Password' => 'password',
    'Type' => 'admin',
    'Class' => 1
]);
```

## Méthodes d'instance

### save

La méthode `save` permet de sauvegarder un compte en base de données.

Syntaxe :
```php
$account->save(): void
```

Exemple :
```php
$account = new Account(1, '2021-01-01', 'admin', 'password', 'admin', 1);
$account->save();
```

### checkPassword

La méthode `checkPassword` permet de vérifier si le mot de passe fourni correspond au mot de passe du compte.

Syntaxe :
```php
$account->checkPassword(string $password): bool
```

Exemple :
```php
$account = Account::getByUsername('admin');
if ($account->checkPassword('password')) {
    echo 'Mot de passe correct';
} else {
    echo 'Mot de passe incorrect';
}
```
