<center><h1>Fonction createIcon</h1></center>

- [Introduction](#introduction)
- [Initialisation](#initialisation)
- [Utilisation](#utilisation)
  - [Syntaxe](#syntaxe)
  - [Paramètres](#paramètres)
  - [Valeur de retour](#valeur-de-retour)
  - [Exemple](#exemple)

# Introduction

La fonction `createIcon` permet de créer une icône en spécifiant son nom.

# Initialisation

Pour pouvoir utiliser la fonction `createIcon`, il suffit d'ajouter le code suivant dans un fichier Twig :

```twig
{% addJS(['js/core/app.js']) %}
```

Par défaut, l'importation est faite dans le fichier `base.twig`.

# Utilisation

## Syntaxe

```typescript
function createIcon(
    name: string
): HTMLElement
```

## Paramètres

- `name` : Le nom de l'icône à créer.

> **Note :** La liste des noms d'icônes disponibles est disponible sur le site [Material Design Icons](https://fonts.google.com/icons).

## Valeur de retour

La fonction renvoie l'élément HTML de l'icône créée.

## Exemple

```typescript
const icon = createIcon('person');
```

![](.assets/icon.png)
