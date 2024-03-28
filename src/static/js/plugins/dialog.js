/// <reference path="../../../static/js/base.js" />

app &&
	app.registerPlugin('dialog', function () {
		const animationDuration = 200;

		// Injecte le CSS nécessaire pour le plugin
		app.injectCSS({
			'.dialog-container': {
				'--dialog-header-height': '4rem',
				'--dialog-footer-height': '4rem',
				'--dialog-header-background': 'var(--background-tertiary-color)',
				'--dialog-body-background': 'var(--background-primary-color)',
				'--dialog-footer-background': 'var(--background-secondary-color)',
				'--dialog-min-height': '15rem',
				'--dialog-min-width': '25rem',
				'--dialog-max-width': '50rem',
				'--dialog-max-height': '40rem',
				'--dialog-header-font-size': '2rem',
				'--dialog-transition-function': 'cubic-bezier(.72,.15,.17,1.69)',
				position: 'fixed',
				top: '0',
				left: '0',
				height: '100%',
				width: '100%',
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center',
				zIndex: '200',
				backdropFilter: 'blur(5px) brightness(0.5)',
				backgroundColor: 'rgba(0, 0, 0, 0.25)',
				transition: `backdrop-filter ${animationDuration}ms var(--dialog-transition-function), background-color ${animationDuration}ms var(--dialog-transition-function)`,
				overscrollBehavior: 'contain'
			},
			'.dialog-container .dialog': {
				minHeight: 'min(var(--dialog-min-height), calc(100% - 1rem))',
				minWidth: 'min(var(--dialog-min-width), calc(100% - 1rem))',
				maxHeight: 'min(var(--dialog-max-height), calc(100% - 1rem))',
				maxWidth: 'min(var(--dialog-max-width), calc(100% - 1rem))',
				backgroundColor: 'var(--dialog-body-background)',
				display: 'flex',
				flexDirection: 'column',
				boxShadow: '0px 0px 5px 0px var(--background-color)',
				zIndex: '200',
				borderRadius: '0.5rem',
				overflowX: 'hidden'
			},
			'.dialog-container .dialog.showing': {
				animation: `dialog-scale ${animationDuration}ms var(--dialog-transition-function) normal forwards`
			},
			'.dialog-container .dialog.closing': {
				animation: `dialog-scale ${animationDuration}ms var(--dialog-transition-function) reverse forwards`
			},
			'.dialog-container .header': {
				width: '100%',
				minHeight: 'var(--dialog-header-height)',
				backgroundColor: 'var(--dialog-header-background)',
				padding: '1.5rem 1.5rem 1rem 1.5rem',
				fontSize: '1.3rem',
				fontWeight: 'bold',
				borderRadius: '0.5rem 0.5rem 0 0',
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'space-between'
			},
			'.dialog-container .header .close': {
				cursor: 'pointer',
				userSelect: 'none',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			},
			'.dialog-container .dialog .body': {
				padding: '1rem',
				textOverflow: 'ellipsis',
				overflowX: 'hidden'
				// overflowY: "scroll",
			},
			'.dialog-container .body': {
				flex: '1'
			},
			'.dialog-container .footer': {
				backgroundColor: 'var(--dialog-footer-background)',
				height: 'var(--dialog-footer-height)',
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'flex-end',
				padding: '0.5rem 1rem 0.5rem 1rem',
				borderRadius: '0 0 0.5rem 0.5rem',
				fontSize: '0.8rem'
			},
			'.dialog-container .button': {
				padding: '0.5rem 1rem',
				borderRadius: '0.5rem',
				cursor: 'pointer',
				userSelect: 'none',
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center',
				transition: 'background-color 250ms ease-in-out',
				color: 'var(--dialog-button-color)'
			},
			'.dialog-container .button.filled': {
				backgroundColor: 'var(--dialog-button-color)',
				color: '#fff'
			},
			'.dialog-container .button .icon': {
				marginRight: '0.5rem',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			},
			'@keyframes dialog-scale': {
				'0%': {
					transform: 'scale(0.8)'
				},
				'100%': {
					transform: 'scale(1)'
				}
			}
		});

		return {
			/**
			 * Crée une boîte de dialogue avec un titre, un contenu et des boutons personnalisables.
			 *
			 * Exemple :
			 * app.dialog.create({
			 *     title: 'Titre de la boîte de dialogue',
			 *	   content: [
			 *         app.createElement('h1', {}, 'Titre'),
			 *		   'Contenu de la boîte de dialogue',
			 *     ],
			 *     buttons: [
			 *         { text: 'Annuler', action: 'close' },
			 *         { text: 'Confirmer', action: (close) => { close(); } }
			 *     ]
			 * }).show();
			 * app.dialog.create({
			 *     title: 'Titre de la boîte de dialogue',
			 *	   content: '<p>Contenu de la boîte de dialogue</p>',
			 *     parseHTML: true
			 * }).show();
			 *
			 * @param {Object} options - Les options de la boîte de dialogue.
			 * @param {string} options.title - Le titre de la boîte de dialogue.
			 * @param {string|Array} options.content - Le contenu de la boîte de dialogue. Peut être une chaîne de caractères ou un tableau d'éléments HTML.
			 * @param {boolean} options.parseHTML - Indique si le contenu doit être interprété comme du code HTML.
			 * @param {Array} options.buttons - Les boutons de la boîte de dialogue.
			 * @param {string} options.buttons.text - Le texte du bouton.
			 * @param {string} options.buttons.icon - L'icône du bouton.
			 * @param {string} options.buttons.color - La couleur du texte et du fond du bouton.
			 * @param {boolean} options.buttons.filled - Indique si le bouton doit être rempli.
			 * @param {string|function} options.buttons.action - L'action à effectuer lors du clic sur le bouton. Peut être une chaîne de caractères ('close') ou une fonction.
			 * @param {function} options.onClose - La fonction à exécuter lors de la fermeture de la boîte de dialogue.
			 * @returns {Object} - L'objet représentant la boîte de dialogue.
			 */
			create({
				title = '',
				content = '',
				parseHTML = false,
				buttons,
				onClose = () => {}
			} = {}) {
				// Si aucun bouton n'est spécifié, on en crée un par défaut
				// Le bouton par défaut est un bouton "OK" qui ferme la boîte de dialogue
				if (!buttons || buttons.length === 0) {
					buttons = [
						{
							text: 'OK',
							icon: 'check',
							color: 'var(--color-blue-600)',
							action: 'close',
							filled: true
						}
					];
				}

				let close;

				// Crée les éléments de la boîte de dialogue
				// Titre dans l'en-tête
				const $title = app.createElement('div', { class: 'title' }, title);
				// Bouton de fermeture dans le coin supérieur droit
				const $close = app.createElement(
					'div',
					{ class: 'close' },
					app.createIcon('close')
				);
				// En-tête de la boîte de dialogue
				const $header = app.createElement('div', { class: 'header' }, $title, $close);
				// Contenu de la boîte de dialogue
				const $body = app.createElement(
					'div',
					{ class: 'body' },
					...(content instanceof Array
						? content
						: [parseHTML ? app.parseHtml(content) : content])
				);

				// Boutons de la boîte de dialogue
				const buttonsElements = [];
				// Crée un élément pour chaque bouton
				for (let button of buttons) {
					// Si le bouton est une chaîne de caractères, on le transforme en objet
					if (typeof button === 'string') button = { text: button };

					// Crée l'icône du bouton si spécifiée
					const $icon =
						button.icon &&
						app.createElement('div', { class: 'icon' }, app.createIcon(button.icon));

					// Crée l'élément du bouton
					const $button = app.createElement(
						'div',
						{
							class: 'button' + (button.filled ? ' filled' : ''),
							style: {
								// Couleur du texte et du fond du bouton
								'--dialog-button-color': button.color
							}
						},
						$icon,
						button.text
					);

					// Ajoute un gestionnaire d'événement pour le bouton
					$button.addEventListener('click', () => {
						// Le texte 'close' est un raccourci pour la fonction de fermeture
						if (button.action === 'close') {
							close();
						} else if (typeof button.action === 'function') {
							button.action(close);
						}
					});

					buttonsElements.push($button);
				}

				// Pied de page
				const $footer = app.createElement('div', { class: 'footer' }, ...buttonsElements);

				// Crée la boîte de dialogue
				const $dialog = app.createElement(
					'div',
					{ class: 'dialog showing' },
					$header,
					$body,
					$footer
				);

				// Conteneur de la boîte de dialogue
				const $dialogContainer = app.createElement(
					'div',
					{ class: 'dialog-container' },
					$dialog
				);

				// Fonction de fermeture de la boîte de dialogue
				close = () => {
					// Ajoute la classe "closing" pour l'animation de fermeture
					$dialog.classList.add('closing');
					setTimeout(() => {
						$dialogContainer.remove();

						// Exécute la fonction onClose
						if (typeof onClose === 'function') onClose();
					}, animationDuration);
				};

				$close.addEventListener('click', close);
				$dialogContainer.addEventListener('click', (e) => {
					if (e.target === $dialogContainer) close();
				});

				// Ajoute un délai avant de retirer la classe "showing" pour permettre l'animation
				setTimeout(() => {
					$dialog.classList.remove('showing');
				}, animationDuration);

				// Retourne l'objet représentant la boîte de dialogue
				//    - element : l'élément HTML de la boîte de dialogue
				//    - close : la fonction pour fermer la boîte de dialogue
				//    - show : la fonction pour afficher la boîte de dialogue
				return {
					element: $dialogContainer,
					close,
					show() {
						document.body.append($dialogContainer);
					}
				};
			},

			/**
			 * Affiche une boîte de dialogue de confirmation.
			 *
			 * @param {Object} options - Les options de la boîte de dialogue.
			 * @param {string} options.title - Le titre de la boîte de dialogue.
			 * @param {string} options.content - Le contenu de la boîte de dialogue.
			 * @param {boolean} options.parseHTML - Indique si le contenu doit être interprété comme du HTML.
			 * @returns {Promise} Une promesse qui se résout lorsque la boîte de dialogue est fermée.
			 */
			confirm({ title = '', content = '', parseHTML = false }) {
				return new Promise((resolve, reject) =>
					// Crée une boîte de dialogue avec deux boutons "Annuler" et "Confirmer"
					this.create({
						title,
						content,
						parseHTML,
						buttons: [
							{
								text: 'Annuler',
								icon: 'close',
								color: 'var(--color-red-600)',
								// La promesse est rejetée si le bouton "Annuler" est cliqué
								action: (close) => {
									close();
									reject();
								}
							},
							{
								text: 'Confirmer',
								icon: 'check',
								color: 'var(--color-blue-600)',
								// La promesse est résolue si le bouton "Confirmer" est cliqué
								action: (close) => {
									close();
									resolve();
								},
								filled: true
							}
						],
						// La promesse est rejetée si la boîte de dialogue est fermée sans cliquer sur un bouton
						onClose: reject
					}).show()
				);
			}
		};
	});
