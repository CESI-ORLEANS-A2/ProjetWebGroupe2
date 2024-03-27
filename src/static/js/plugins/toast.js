/// <reference path="../../../static/js/base.js" />

app &&
	app.registerPlugin('toast', function () {
		const toastContainer = document.getElementById('toasts');

		// Injecter le CSS pour les toasts.
		this.injectCSS({
			'#toasts': {
				position: 'fixed',
				display: 'flex',
				MsFlexDirection: 'column',
				flexDirection: 'column',
				rowGap: '1.5rem',
				top: '1rem',
				right: '1rem',
				maxWidth: ' min(25rem, calc(100% - 2rem))',
				width: '100%',
				zIndex: '200',
				maxHeight: 'calc(100vh - 2rem)'
			},
			'#toasts:empty': {
				display: 'none'
			},
			'.toast': {
				display: 'flex',
				flexDirection: 'column',
				background: 'var(--toast-background-color)',
				borderRadius: '0.5rem',
				overflow: 'hidden',
				animation: 'toast-slide-in 100ms',
				flex: '1 0',
				boxShadow: '0 0 0.5rem rgba(0, 0, 0, 0.3)'
			},
			'.toast.closing': {
				transition: 'transform 100ms linear',
				transform: 'translateY(-100%)'
			},
			'.toast .toast-content': {
				display: 'flex',
				flexDirection: 'row',
				padding: '1rem 1rem calc(1rem - 3px) 1rem',
				alignItems: 'center',
				columnGap: '0.75rem'
			},
			'.toast .icon': {
				fontSize: '1.5rem',
				padding: '0.5rem',
				borderRadius: '100%',
				backgroundColor: 'var(--toast-color)',
				// width: "1.5rem",
				// height: "1.5rem",
				userSelect: 'none',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			},
			'.toast .body': {
				flex: '1',
				textOverflow: 'ellipsis',
				overflow: 'hidden'
			},
			'.toast .close': {
				cursor: 'pointer',
				userSelect: 'none',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			},
			'.toast .progress-bar': {
				width: '100%',
				height: '3px',
				animationName: 'toast-progress-bar',
				animationDirection: 'normal',
				animationFillMode: 'forwards',
				animationIterationCount: '1',
				animationTimingFunction: 'linear',
				backgroundColor: 'var(--toast-progress-bar-color)',
				borderTopRightRadius: '0.5rem',
				borderBottomRightRadius: '0.5rem'
			},
			'@keyframes toast-progress-bar': {
				'0%': {
					width: '0%'
				},
				'100%': {
					width: '100%'
				}
			},
			'@keyframes toast-slide-in': {
				'0%': {
					transform: 'translateX(100%)'
				},
				'100%': {
					transform: 'translateX(0%)'
				}
			}
		});

		return {
			/**
			 * Affiche un message de succès.
			 * 
			 * Exemple :
			 * app.toast.success("Opération réussie !");
			 * app.toast.success("Opération réussie !", { duration: 5000 }); 
			 *
			 * @param {string} message - Le message à afficher.
			 * @param {Object} options - Les options de configuration (facultatif).
			 * @returns {Object} - L'objet créé pour le message de succès.
			 */
			success(message, options = {}) {
				return this.create(message, { type: 'success', ...options });
			},
			/**
			 * Affiche un message d'erreur.
			 *
			 * @param {string} message - Le message d'erreur à afficher.
			 * @param {Object} options - Les options de configuration (facultatif).
			 * @returns {Object} - L'objet créé pour le message d'erreur.
			 */
			error(message, options = {}) {
				return this.create(message, { type: 'error', duration: 5000, ...options });
			},
			/**
			 * Affiche un message de type "warning" dans un toast.
			 *
			 * @param {string} message - Le message à afficher.
			 * @param {object} options - Les options de configuration du toast (facultatif).
			 * @returns {object} - Le toast créé.
			 */
			warning(message, options = {}) {
				return this.create(message, { type: 'warning', ...options });
			},
			/**
			 * Affiche un message d'information.
			 *
			 * @param {string} message - Le message à afficher.
			 * @param {Object} options - Les options de configuration (facultatif).
			 * @returns {Object} - L'objet créé pour le message d'information.
			 */
			info(message, options = {}) {
				return this.create(message, { type: 'info', ...options });
			},
			/**
			 * Crée un toast avec les options spécifiées.
			 *
			 * @param {string} message - Le message du toast.
			 * @param {object} options - Les options du toast.
			 * @param {string} [options.type='success'] - Le type du toast ('success', 'error', 'warning' ou 'info').
			 * @param {number} [options.duration=3000] - La durée d'affichage du toast en millisecondes.
			 * @param {boolean} [options.filled=false] - Indique si la barre de progression du toast est remplie.
			 * @returns {object} - Un objet contenant les méthodes et l'élément du toast.
			 */
			create(message, { type = 'success', duration = 3000, filled = false } = {}) {
				/** @type {IconKeywordMap} */
				let icon;
				let color;

				// Déterminer l'icône et la couleur en fonction du type.
				switch (type) {
					case 'success':
						icon = 'check';
						color = '--color-green-600';
						break;
					case 'error':
						icon = 'error';
						color = '--color-red-600';
						break;
					case 'warning':
						icon = 'warning';
						color = '--color-yellow-600';
						break;
					default:
						icon = 'info';
						color = '--color-blue-600';
						break;
				}

				// Créer l'icon, le corps, le bouton de fermeture et le contenu du toast.
				const $icon = app.createElement('div', { class: 'icon' }, app.createIcon(icon));
				const $body = app.createElement('div', { class: 'body' }, message);
				const $close = app.createElement(
					'div',
					{ class: 'close' },
					app.createIcon('close')
				);
				const $toastContent = app.createElement(
					'div',
					{ class: 'toast-content' },
					$icon,
					$body,
					$close
				);

				// Créer la barre de progression et le conteneur de la barre de progression.
				const $progressBar = app.createElement('div', { class: 'progress-bar' });
				const $progressBarContainer = app.createElement(
					'div',
					{ class: 'progress-bar-container' },
					$progressBar
				);

				// Créer le toast.
				const $toast = app.createElement(
					'div',
					{
						class: 'toast',
						style: {
							// Modifier les variables CSS en fonction du type et de la couleur.
							'--toast-color': `var(${color})`,
							'--toast-progress-bar-color': filled ? '#fff' : 'var(--toast-color)',
							'--toast-background-color': filled
								? `var(--toast-color)`
								: 'var(--background-tertiary-color)'
						},
						cRipple: true
					},
					$toastContent,
					$progressBarContainer
				);

				// Fonction pour fermer le toast.
				const close = () => {
					$toast.classList.add('closing');
					if (timeoutId) clearTimeout(timeoutId);
					setTimeout(() => {
						$toast.remove();

						app.dispatchEvent($toast, 'closed');
					}, 100);
				};

				// Initialiser les variables pour la barre de progression.
				let timeRemaining = duration;
				let lastPause = Date.now();
				let currentState = (1 - timeRemaining / duration).toFixed(2);
				let timeoutId = setTimeout(close, duration);

				// Modifier la durée et le délai de l'animation de la barre de progression.
				$progressBar.style.animationDuration = `${duration}ms`;
				$progressBar.style.animationDelay = `-${duration * currentState}ms`;

				$close.addEventListener('click', close);
				$toast.addEventListener('mouseenter', () => {
					// Si la souris est sur le toast, mettre en pause la barre de progression.

					$progressBar.style.animationPlayState = 'paused';

					timeRemaining = timeRemaining - (Date.now() - lastPause);

					if (timeoutId) clearTimeout(timeoutId);
				});
				$toast.addEventListener('mouseleave', () => {
					// Si la souris quitte le toast, reprendre la barre de progression.

					lastPause = Date.now();
					$progressBar.style.animationPlayState = 'running';
					timeoutId = setTimeout(close, timeRemaining);
				});

				toastContainer.append($toast);

				// Retourne l'objet du toast.
				//    - close: ferme le toast.
				//    - pause: met en pause la barre de progression.
				//    - resume: reprend la barre de progression.
				//    - element: l'élément du toast.
				return {
					close,
					pause: () => {
						$toast.dispatchEvent(new Event('mouseenter'));
					},
					resume: () => {
						$toast.dispatchEvent(new Event('mouseleave'));
					},
					element: $toast
				};
			}
		};
	});
