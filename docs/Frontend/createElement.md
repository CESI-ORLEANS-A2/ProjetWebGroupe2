<center><h1>Fonction createElement</h1></center>

- [Introduction](#introduction)
- [Initialisation](#initialisation)
- [Utilisation](#utilisation)
  - [Syntaxe](#syntaxe)
  - [Paramètres](#paramètres)
  - [Valeur de retour](#valeur-de-retour)
  - [Exemple](#exemple)

# Introduction

La fonction `createElement` permet de créer un élément HTML en spécifiant son nom, ses attributs et son contenu.

# Initialisation

Pour pouvoir utiliser la fonction `createElement`, il suffit d'ajouter le code suivant dans un fichier Twig :

```twig
{% addJS(['js/core/app.js']) %}
```

Par défaut, l'importation est faite dans le fichier `base.twig`.

# Utilisation

## Syntaxe

```typescript
function createElement(
	tagName: string,
	attributes?: {
		class: string;
		style: string;
		on: {
			[key: string]: (event: Event) => void;
		};
		once: {
			[key: string]: (event: Event) => void;
		};
		[key: string]: string;
	},
	content?: string | HTMLElement | Array<string | HTMLElement>
): HTMLElement;
```

## Paramètres

-   `tagName` : Le nom de la balise HTML à créer.
-   `attributes` : Les attributs de l'élément. Peut contenir les propriétés `class`, `style`, `on` et `once`.
    -   `class` : Les classes CSS de l'élément.
    -   `style` : Les styles CSS de l'élément.
    -   `on` : Les écouteurs d'événements de l'élément.
    -   `once` : Les écouteurs d'événements à déclencher une seule fois.
    -   `[key]` : Les autres attributs de l'élément comme `id`, `name`, `value`, etc.
-   `content` : Le contenu de l'élément. Peut être une chaîne de caractères, un élément HTML ou un tableau de chaînes de caractères et d'éléments HTML.

## Valeur de retour

La fonction renvoie l'élément HTML créé avec les attributs et le contenu spécifiés.

## Exemple

```typescript
const button = createElement(
	'button',
	{
		class: 'btn btn-primary',
		on: {
			click: (event) => {
				console.log('Button clicked');
			}
		}
	},
	'Click me'
);

document.body.appendChild(button);
```
