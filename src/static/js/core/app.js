/// <reference path="../../../types/App.d.ts" />

(function () {
	/**
	 * Classe représentant l'application.
	 */
	class App {
		components = {};
		plugins = {};

		callbacks = {
			onload: []
		};

		constructor() {}

		/**
		 * Enregistre un composant dans l'application.
		 *
		 * => fonction interne
		 *
		 * @param {string} name - Le nom du composant.
		 * @param {Function} initExistingComponent - La fonction d'initialisation du composant existant.
		 * @param {Function} create - La fonction de création du composant.
		 * @param {Object} [data={}] - Les données par défaut du composant.
		 * @returns {Object} - Le composant enregistré.
		 */
		registerComponent(name, initExistingComponent, create, data = {}) {
			// Crée le nom complet du composant, ce nom est le nom du composant HTML
			const fullName = 'c-' + name;

			// Ajoute le composant à la liste des composants
			this.components[name] = {
				name,
				fullName,
				init: initExistingComponent,
				create,
				instances: [],
				defaultData: data
			};

			// Initialise tous les éléments de composant correspondant au nom donné
			this.initAllComponentElements(name);

			return this.components[name];
		}

		/**
		 * Crée un composant avec le nom spécifié, les arguments et les données optionnelles.
		 *
		 * Exemple :
		 * app.createComponent('textarea', { id: 'username', label: 'Nom d'utilisateur' });
		 *
		 * @param {string} name - Le nom du composant.
		 * @param {Object} args - Les arguments du composant.
		 * @param {Object} data - Les données du composant.
		 * @returns {Object} - Le composant créé.
		 */
		createComponent(name, args = {}, data = {}) {
			// Créé un objet représentant le composant
			const component = {
				components: this.components[name],
				element: null,
				data: Object.assign({}, this.components[name]?.defaultData || {}, data)
			};

			// Crée l'élément du composant en utilisant la fonction de création du composant
			// fournie lors de l'enregistrement du composant
			component.element = this.components[name].create?.(component, args);

			// Initialise le composant
			this.components[name].init?.call(this, component);
			// Ajoute le composant à la liste des instances du composant
			this.components[name].instances.push(component);

			return component;
		}

		/**
		 * Initialise tous les éléments de composant correspondant au nom donné.
		 *
		 * => fonction interne
		 *
		 * @param {string} name - Le nom du composant.
		 * @returns {Object} - Le composant initialisé.
		 */
		initAllComponentElements(name) {
			if (!this.components[name]) return;

			// Sélectionne tous les éléments de composant correspondant au nom donné
			let elements = document.querySelectorAll(`.${this.components[name].fullName}`);
			for (let element of elements) {
				// Initialise chaque élément de composant
				this.initComponentElement(name, element);
			}

			return this.components[name];
		}

		/**
		 * Initialise un élément de composant.
		 *
		 * => fonction interne
		 *
		 * @param {string} name - Le nom du composant.
		 * @param {HTMLElement} element - L'élément HTML associé au composant.
		 */
		initComponentElement(name, element) {
			if (!this.components[name]) return;
			// Si l'élément est déjà initialisé, ne pas le réinitialiser
			if (element.__component) return;

			// Crée un objet représentant le composant
			const component = {
				components: this.components[name],
				element: element,
				data:
					this.components[name].defaultData &&
					Object.assign({}, this.components[name].defaultData)
			};

			// Initialise le composant
			this.components[name].init?.call(this, component);
			// Ajoute le composant à la liste des instances du composant
			this.components[name].instances.push(component);
			element.__component = component;
		}

		/**
		 * Enregistre un plugin dans l'application.
		 *
		 * => fonction interne
		 *
		 * @param {string} name - Le nom du plugin.
		 * @param {Function} init - La fonction d'initialisation du plugin.
		 * @returns {Object} - L'objet représentant le plugin enregistré.
		 * @throws {Error} - Si le nom du plugin est déjà utilisé.
		 */
		registerPlugin(name, init) {
			// Si le nom du plugin est déjà utilisé, lancer une erreur
			if (this[name]) throw new Error(`Plugin name "${name}" is already taken`);

			// Crée un objet représentant le plugin
			const plugin = {
				plugins: this.plugins,
				name,
				init
			};

			// Enregistre le plugin dans l'application
			this[name] = init.call(this, plugin);
			// Ajoute le plugin à la liste des plugins
			this.plugins[name] = plugin;

			return plugin;
		}

		/**
		 * Crée un élément HTML avec les attributs et les enfants spécifiés.
		 *
		 * Exemple :
		 * app.createElement(
		 *     'div',
		 *     {
		 *         id: 'container',
		 *         class: 'container'
		 *     },
		 *     'Hello, world!',
		 *     app.createElement('button', { on: {
		 *          click : () => alert('Hello, world!')
		 *     }}, 'Click me')
		 * );
		 *
		 * @param {string} name - Le nom de l'élément HTML à créer.
		 * @param {Object} [attributes={}] - Les attributs de l'élément HTML.
		 * @param {...(Element|string|Array)} children - Les enfants de l'élément HTML.
		 * @returns {Element} L'élément HTML créé.
		 */
		createElement(name, attributes = {}, ...children) {
			// Crée un élément HTML avec le type spécifié
			let element = document.createElement(name);

			// Ajoute les écouteurs d'événements spécifiés
			if (attributes.on) {
				Object.entries(attributes.on).forEach(([eventType, callbacks]) => {
					if (!(callbacks instanceof Array)) callbacks = [callbacks];

					callbacks.forEach((callback) => element.addEventListener(eventType, callback));
				});
				delete attributes.on;
			}
			// Ajoute les écouteurs d'événements spécifiés qui ne doivent être exécutés qu'une seule fois
			if (attributes.once) {
				Object.entries(attributes.once).forEach(([eventType, callbacks]) => {
					if (!(callbacks instanceof Array)) callbacks = [callbacks];

					callbacks.forEach((callback) => {
						eventListener = element.addEventListener(eventType, callback, {
							signal: new AbortController().signal
						});
					});
				});
				delete attributes.once;
			}
			// Ajoute les styles spécifiés
			if (attributes.style && typeof attributes.style === 'object') {
				element.style = this.stringifyInlineCSS(attributes.style);
				delete attributes.style;
			}

			// Ajoute les autres attributs spécifiés
			Object.entries(attributes).forEach(([attributeName, attributeValue]) => {
				// Si la valeur de l'attribut est un nombre et qu'elle est NaN,
				// ou si la valeur de l'attribut est false, undefined, null ou 'false', ne pas ajouter l'attribut
				if (
					(typeof attributeName === 'number' && isNaN(attributeValue)) ||
					attributeValue === false ||
					attributeValue === undefined ||
					attributeValue === null ||
					attributeValue === 'false'
				)
					return;

				// Ajoute l'attribut à l'élément, en convertissant le nom de l'attribut en notation kebab-case
				element.setAttribute(
					this.camelToKebab(attributeName),
					typeof attributeValue === 'string'
						? attributeValue
						: JSON.stringify(attributeValue)
				);
			});

			// Ajoute les enfants spécifiés à l'élément
			element.append(
				...children
					// Filtre les enfants pour supprimer les valeurs indéfinies, nulles, NaN et fausses
					.filter(
						(elem) =>
							elem !== undefined && elem !== null && elem !== NaN && elem !== false
					)
					// Aplatit les tableaux d'éléments
					.reduce(
						(prev, curr) => (
							curr instanceof Array ? prev.push(...curr) : prev.push(curr), prev
						),
						[]
					)
					// Convertit les éléments en chaînes de caractères si ce ne sont pas des éléments HTML
					.map((elem) =>
						elem instanceof Element || typeof elem === 'string'
							? elem
							: JSON.stringify(elem)
					)
			);

			return element;
		}

		/**
		 * Crée une icône avec la classe 'material-icons'.
		 *
		 * @param {string} name - Le nom de l'icône.
		 * @returns {HTMLElement} - L'élément span contenant l'icône.
		 */
		createIcon(name) {
			// Crée un élément span avec la classe 'material-icons' et le nom de l'icône spécifié
			return this.createElement('span', { class: 'material-icons' }, name);
		}

		/**
		 * Converti un objet de style en une chaîne de caractères représentant du CSS.
		 *
		 * @param {Object} style - L'objet de style à convertir.
		 * @returns {string} La chaîne de caractères représentant du CSS.
		 */
		stringifyCSS(style) {
			// Converti l'objet de style en une chaîne de caractères représentant du CSS
			return Object.entries(style)
				.map(([key, value]) => {
					// Si la valeur est une chaîne de caractères,
					// retourne la chaîne de caractères représentant une règle CSS
					if (typeof value === 'string') return `${key}{${value}}`;
					// Si la valeur est un nombre, retourne la chaîne de caractères représentant une règle CSS
					else if (value instanceof Array) return `${key}{${value.join('\n')}}`;
					// Si la valeur est un objet, retourne la chaîne de caractères représentant une règle CSS
					else
						return `${key}{${Object.entries(value)
							.map(([propertyName, propertyValue]) => {
								if (
									typeof propertyValue === 'string' ||
									typeof propertyValue === 'number'
								)
									return `${this.camelToKebab(
										propertyName,
										false
									)}:${propertyValue};`;
								else
									return `${this.camelToKebab(
										propertyName,
										false
									)}{${Object.entries(propertyValue)
										.map(([k, v]) => `${k}:${v};`)
										.join('')}};`;
							})
							.join('')}}`;
				})
				.join('');
		}

		/**
		 * Convertit un objet de style en une chaîne de caractères représentant du CSS en ligne.
		 * Dans le but d'être utilisé dans l'attribut style d'un élément HTML.
		 *
		 * @param {Object} style - L'objet de style à convertir.
		 * @returns {string} La chaîne de caractères représentant du CSS en ligne.
		 */
		stringifyInlineCSS(style) {
			return Object.entries(style)
				.map(([key, value]) => {
					if (typeof value === 'string' || typeof value === 'number')
						return `${this.camelToKebab(key, false)}:${value};`;
					if (value instanceof Array)
						return `${this.camelToKebab(key, false)}:${value.join(' ')};`;
				})
				.join('');
		}

		/**
		 * Injecte une feuille de style CSS dans le document.
		 *
		 * @param {string} css - Le code CSS à injecter.
		 * @returns {HTMLStyleElement} - L'élément <style> créé et injecté dans le <head> du document.
		 */
		injectCSS(css) {
			let style = this.createElement('style');
			style.innerHTML = this.stringifyCSS(css);
			document.head.append(style);
			return style;
		}

		/**
		 * Convertit une chaîne de caractères en notation kebab-case à partir de la notation camelCase.
		 *
		 * @param {string} text - La chaîne de caractères en notation camelCase à convertir.
		 * @param {boolean} [excludeFirstChar=true] - Indique si le premier caractère doit être exclu de la conversion.
		 * @returns {string} La chaîne de caractères convertie en notation kebab-case.
		 */
		camelToKebab(text, excludeFirstChar = true) {
			return text.replace(
				new RegExp((excludeFirstChar ? '(?<!^)' : '') + '\\p{Lu}', 'gmu'),
				(match) => '-' + match.toLowerCase()
			);
		}

		/**
		 * Parse le contenu HTML fourni et retourne les éléments DOM correspondants.
		 * @param {string} string - Le contenu HTML à analyser.
		 * @returns {Element|HTMLCollection} - Les éléments DOM correspondants au contenu HTML.
		 */
		parseHtml(string) {
			// Parse le contenu HTML fourni et retourne les éléments DOM correspondants
			// On utilise l'API DOMParser pour analyser le contenu HTML
			let elements = new DOMParser().parseFromString(string, 'text/html')?.body.children;
			if (elements.length === 1) return elements[0];
			return elements;
		}

		/**
		 * Distribue un événement personnalisé sur l'élément spécifié.
		 *
		 * @param {string|NodeList|Array} element - L'élément ou les éléments sur lesquels distribuer l'événement.
		 * @param {string} name - Le nom de l'événement personnalisé.
		 * @param {any} data - Les données à transmettre avec l'événement.
		 */
		dispatchEvent(element, name, data) {
			// Si l'élément est une chaîne de caractères, sélectionne tous les éléments
			// correspondant au sélecteur
			if (typeof element === 'string') {
				element = document.querySelectorAll(element);
			}
			// Si l'élément est une NodeList ou un tableau, distribue l'événement personnalisé
			// sur chaque élément
			if (element instanceof NodeList || element instanceof Array) {
				element.forEach((elem) =>
					elem.dispatchEvent(new CustomEvent(name, { detail: data }))
				);
			} else {
				element.dispatchEvent(new CustomEvent(name, { detail: data }));
			}
		}

		/**
		 * Ajoute une fonction de rappel à exécuter lorsque la page est entièrement chargée.
		 *
		 * @param {Function} callback - La fonction de rappel à exécuter.
		 */
		onload(callback) {
			this.callbacks.onload.push(callback);
		}

		click(element, callback) {
			if (!element || element instanceof HTMLElement)
				throw new Error('Invalid element provided');

			if (!callback || typeof callback !== 'function') {
				element.click();
			} else {
				element.addEventListener('click', callback);
			}

			return element;
		}
	}

	// window.app = new App();
	globalThis.app = new App();

	// Exécuter le code lorsque le DOM est chargé et que les modules ont fini de charger
	// Les fonctions de rappel ajoutées avec app.onload() seront exécutées.
	document.addEventListener('DOMContentLoaded', function () {
		app.callbacks.onload.forEach((callback) => callback.call(this, ...arguments));
	});
})();

/**
 * @var {App} app
 */

/**
 * @memberof globalThis
 * @prop {App} app
 */

globalThis.app;
