/**
 * @typedef {Object} Tooltip
 * @property {HTMLElement} element - L'élément du tooltip.
 * @property {string | HTMLElement} content - Le contenu du tooltip.
 * @property {boolean} parseHTML - Indique si le contenu doit être interprété comme du HTML.
 * @property {number} id - L'identifiant du tooltip.
 * @property {string} position - La position du tooltip.
 * @property {HTMLElement} target - L'élément cible du tooltip.
 * @property {number} offset - Le décalage du tooltip.
 * @property {boolean} styled - Indique si le tooltip est stylisé.
 * @property {HTMLElement} appendTo - L'élément auquel le tooltip est ajouté.
 * @property {string} align - L'alignement du tooltip.
 * @property {boolean} fullWidth - Indique si le tooltip occupe toute la largeur.
 * @property {function} remove - Supprime le tooltip. Identique à hide.
 * @property {function} hide - Masque le tooltip. Identique à remove.
 */

app &&
	app.registerPlugin('tooltip', function () {
		let lastTooltipId = -1;
		let timeout = null;

		/**
		 * Supprime un tooltip.
		 * @param {Tooltip} tooltip - Le tooltip à supprimer.
		 */
		function removeTooltip(tooltip) {
			// Si le tooltip n'a pas encore été affiché, on annule son affichage.
			if (timeout) clearTimeout(timeout);

			// On supprime l'élément du tooltip et on supprime les références.
			tooltip.element.remove();
			tooltip.element.__tooltip = null;
			tooltip.target.__tooltip = null;
			tooltip.target.removeAttribute('data-tooltip');
		}

		/**
		 * Crée un tooltip.
		 * @param {Object} param0 - Les options du tooltip.
		 * @param {string | HTMLElement} param0.content - Le contenu du tooltip.
		 * @param {boolean} param0.parseHTML - Indique si le contenu doit être interprété comme du HTML.
		 * @param {HTMLElement} param0.target - L'élément cible du tooltip.
		 * @param {string} param0.position - La position du tooltip.
		 * @param {number} param0.offset - Le décalage du tooltip.
		 * @param {boolean} param0.styled - Indique si le tooltip est stylisé.
		 * @param {HTMLElement} param0.appendTo - L'élément auquel le tooltip est ajouté.
		 * @param {string} param0.align - L'alignement du tooltip.
		 * @param {boolean} param0.fullWidth - Indique si le tooltip occupe toute la largeur.
		 * @param {number} param0.delay - Le délai avant l'affichage du tooltip.
		 * @returns {Tooltip} - Le tooltip créé.
		 */
		function createTooltip({
			content,
			parseHTML = false,
			target,
			position = 'top',
			offset = 2,
			styled = true,
			appendTo = document.body,
			align = 'center',
			fullWidth = false,
			delay = 500
		} = {}) {
			// On vérifie que la position est valide.
			if (!['top', 'right', 'bottom', 'left'].includes(position)) position = 'top';

			const id = ++lastTooltipId;

			// On crée le tooltip.
			// Le proxy permet de modifier les propriétés du tooltip après sa création.
			// Les modifications sont répercutées sur l'élément du tooltip.
			/** @type {Tooltip} */
			const tooltip = new Proxy(
				{
					element: app.createElement(
						'div',
						{
							class: 'c-tooltip',
							dataId: id
						},
						app.createElement(
							'div',
							{ class: 'c-tooltip-content' },
							parseHTML ? app.parseHtml(content) : content
						),
						app.createElement('div', { class: 'c-tooltip-arrow' })
					),
					content,
					parseHTML: !!parseHTML,
					id,
					position,
					target,
					offset,
					styled: !!styled,
					appendTo,
					align,
					fullWidth: !!fullWidth,
					remove: () => removeTooltip(tooltip),
					hide: () => removeTooltip(tooltip)
				},
				{
					set(obj, prop, value) {
						switch (prop) {
							// Si la propriété est la position, on vérifie qu'elle est valide et on réajuste la position du tooltip.
							case 'position': {
								if (obj.position !== value) {
									if (!['top', 'right', 'bottom', 'left'].includes(position)) {
										return false;
									}
									obj.position = value;
									positionTooltip(obj);
									return true;
								}
								return false;
							}
							// Si la propriété est la cible, on vérifie qu'elle est valide et on réajuste la position du tooltip.
							case 'target': {
								if (obj.target !== value) {
									if (value) {
										obj.target.removeAttribute('data-tooltip');
										value.setAttribute('data-tooltip', id);
										obj.target = value;
										document.body.appendChild(obj.element);
										positionTooltip(obj);
									} else {
										obj.element.remove();
										obj.target = null;
									}
									return true;
								}
								return false;
							}
							// Si la propriété est le contenu, on met à jour le contenu du tooltip.
							case 'content': {
								if (obj.content !== value) {
									if (value) {
										obj.content = value;
										const $content =
											obj.element.querySelector('.c-tooltip-content');
										$content.innerHTML = '';
										if (value instanceof HTMLElement) {
											$content.appendChild(value);
										} else if (typeof value === 'string') {
											if (obj.parseHTML) {
												$content.innerHTML = value;
											} else {
												$content.textContent = value;
											}
										}
										positionTooltip(obj);
									} else {
										obj.element.remove();
									}
									return true;
								}
								return false;
							}
							// Si la propriété est le parseHTML, on met à jour le contenu du tooltip.
							case 'parseHTML': {
								if (obj.parseHTML !== value) {
									obj.parseHTML = !!value;
									if (typeof obj.content === 'string') {
										const $content =
											obj.element.querySelector('.c-tooltip-content');
										if (value) {
											$content.innerHTML = obj.content;
										} else {
											$content.textContent = obj.content;
										}
									}
									positionTooltip(obj);
									return true;
								}
								return false;
							}
							// Si la propriété est l'offset, on met à jour le décalage du tooltip.
							case 'offset': {
								if (obj.offset !== value) {
									if (
										typeof value !== 'number' ||
										isNaN(value) ||
										!isFinite(value)
									) {
										value = 0;
									}
									obj.offset = value;
									positionTooltip(obj);
									return true;
								}
								return false;
							}
							// Si la propriété est le styled, on met à jour le style du tooltip.
							case 'styled': {
								if (obj.styled !== value) {
									obj.styled = !!value;
									obj.element.classList.toggle('styled', obj.styled);
									return true;
								}
								return false;
							}
							// Si la propriété est le appendTo, on met à jour l'élément auquel le tooltip est ajouté.
							case 'appendTo': {
								if (obj.appendTo !== value) {
									if (value) {
										obj.appendTo = value;
										value.appendChild(obj.element);
										positionTooltip(obj);
									}
									return true;
								}
								return false;
							}
							// Si la propriété est l'align, on met à jour l'alignement du tooltip.
							case 'align': {
								if (obj.align !== value) {
									if (!['border', 'center'].includes(value)) {
										return false;
									}
									obj.align = value;
									positionTooltip(obj);
									return true;
								}
								return false;
							}
							// Si la propriété est le fullWidth, on met à jour la largeur du tooltip.
							case 'fullWidth': {
								if (obj.fullWidth !== value) {
									obj.fullWidth = !!value;
									obj.element.style.minWidth = obj.fullWidth
										? obj.target?.clientWidth + 'px'
										: '';
									positionTooltip(obj);
									return true;
								}
								return false;
							}
							default:
								return false;
						}
					}
				}
			);

			// On ajoute le tooltip à l'élément cible.
			tooltip.element.__tooltip = tooltip;
			tooltip.target.__tooltip = tooltip;
			tooltip.element.classList.toggle('styled', tooltip.styled);
			tooltip.element.style.minWidth = tooltip.fullWidth
				? tooltip.target?.clientWidth + 'px'
				: '';
			tooltip.target.setAttribute('data-tooltip', id);

			// On affiche le tooltip après un délai.
			timeout = setTimeout(() => {
				appendTo.append(tooltip.element);

				positionTooltip(tooltip);
			}, delay);

			return tooltip;
		}

		/**
		 * Positionne un tooltip par rapport à sa cible.
		 * Cette fonction est appelée à chaque fois que la fenêtre est redimensionnée ou que la page est défilée ou que le tooltip est modifié.
		 * Elle prend en charge les cas où le tooltip est en dehors de la fenêtre.
		 * @param {Tooltip} tooltip
		 * @return {void}
		 */
		function positionTooltip(tooltip) {
			if (!tooltip.target) return;

			// On récupère les dimensions et les positions de la cible et du tooltip.
			const targetRect = tooltip.target.getBoundingClientRect();
			const tooltipRect = tooltip.element.getBoundingClientRect();

			let top;
			let left;

			// On positionne le tooltip en fonction de la position.
			switch (tooltip.position) {
				case 'top':
					top = targetRect.top - tooltipRect.height - tooltip.offset;
					left =
						targetRect.left +
						(targetRect.width / 2 - tooltipRect.width / 2) *
							(tooltip.align !== 'border');

					// On vérifie que le tooltip ne dépasse pas de la fenêtre.
					// Si c'est le cas, on change la position du tooltip.
					if (left < 0) left = 0;
					// Si le tooltip est en dehors de la fenêtre, on le positionne en dessous de la cible.
					if (top < 0 + 80) {
						positionTooltip({ ...tooltip, position: 'bottom' });
						return;
					}

					break;
				case 'right':
					top =
						targetRect.top +
						(targetRect.height / 2 - tooltipRect.height / 2) *
							(tooltip.align !== 'border');
					left = targetRect.right + tooltip.offset;

					// On vérifie que le tooltip ne dépasse pas de la fenêtre.
					// Si c'est le cas, on change la position du tooltip.
					if (left + tooltipRect.width > window.innerWidth) {
						positionTooltip({ ...tooltip, position: 'left' });
						return;
					}

					break;
				case 'bottom':
					top = targetRect.bottom + tooltip.offset;
					left =
						targetRect.left +
						(targetRect.width / 2 - tooltipRect.width / 2) *
							(tooltip.align !== 'border');

					// On vérifie que le tooltip ne dépasse pas de la fenêtre.
					// Si c'est le cas, on change la position du tooltip.
					if (left < 0) left = 0;
					// Si le tooltip est en dehors de la fenêtre, on le positionne au-dessus de la cible.
					if (top + tooltipRect.height > window.innerHeight) {
						positionTooltip({ ...tooltip, position: 'top' });
						return;
					}

					break;
				case 'left':
					top =
						targetRect.top +
						(targetRect.height / 2 - tooltipRect.height / 2) *
							(tooltip.align !== 'border');
					left = targetRect.left - tooltipRect.width - tooltip.offset;

					// On vérifie que le tooltip ne dépasse pas de la fenêtre.
					// Si c'est le cas, on change la position du tooltip.
					if (left < 0) {
						positionTooltip({ ...tooltip, position: 'right' });
						return;
					}

					break;
			}

			// On positionne le tooltip.
			tooltip.element.style.top = `${top}px`;
			tooltip.element.style.left = `${left}px`;
			tooltip.element.classList.remove('top', 'right', 'bottom', 'left');
			tooltip.element.classList.add(tooltip.position);
		}

		app.injectCSS({
			'.c-tooltip': {
				pointerEvents: 'none',
				position: 'fixed',
				// zIndex: '99', // 100 - 1 Sinon il passe par dessus le header
				zIndex: '9999',
				borderRadius: '.25rem',
				boxShadow: '0 0 .5rem rgba(0, 0, 0, .35)',
				display: 'inline-block',
				whiteSpace: 'nowrap'
			},
			'.c-tooltip.styled': {
				color: '#fff',
				padding: '.5rem',
				background: 'var(--color-blue-800)'
			},
			'.c-tooltip.styled .c-tooltip-arrow': {
				position: 'absolute',
				width: '16px',
				height: '16px',
				borderStyle: 'solid',
				borderColor: 'transparent',
				borderWidth: '8px 8px 0',
				borderTopColor: 'var(--color-blue-800)'
			},
			'.c-tooltip.styled.top': {
				transform: 'translate(0, -8px)'
			},
			'.c-tooltip.styled.top .c-tooltip-arrow': {
				transform: 'translate(-8px, 16px)',
				bottom: 0,
				left: '50%'
			},
			'.c-tooltip.styled.right': {
				transform: 'translate(8px, 0)'
			},
			'.c-tooltip.styled.right .c-tooltip-arrow': {
				transform: 'translate(-16px, -8px) rotate(90deg)',
				left: 0,
				top: '50%'
			},
			'.c-tooltip.styled.bottom': {
				transform: 'translate(0, 8px)'
			},
			'.c-tooltip.styled.bottom .c-tooltip-arrow': {
				transform: 'translate(-8px, -16px) rotate(180deg)',
				top: 0,
				left: '50%'
			},
			'.c-tooltip.styled.left': {
				transform: 'translate(-8px, 0)'
			},
			'.c-tooltip.styled.left .c-tooltip-arrow': {
				transform: 'translate(16px, -8px) rotate(-90deg)',
				right: 0,
				top: '50%'
			}
		});

		// On positionne les tooltips à chaque fois que la page est défilée ou redimensionnée.
		document.body.addEventListener('scroll', () => {
			document.querySelectorAll('[data-tooltip]').forEach((target) => {
				let element = document.querySelector(
					`.c-tooltip[data-id="${target.dataset.tooltip}"]`
				);
				const tooltip = element?.__tooltip;

				if (tooltip) {
					positionTooltip(tooltip);
				}
			});
		});
		window.addEventListener('resize', () => {
			document.querySelectorAll('[data-tooltip]').forEach((target) => {
				let element = document.querySelector(
					`.c-tooltip[data-id="${target.dataset.tooltip}"]`
				);
				const tooltip = element?.__tooltip;

				if (tooltip) {
					positionTooltip(tooltip);
				}
			});
		});

		return {
			/**
			 * Affiche un tooltip.
			 * @param {HTMLElement | string} target - L'élément auquel le tooltip sera ajouté.
			 * @param {string | HTMLElement | Array<HTMLElement | string>} content - Le contenu du tooltip.
			 * @param {{}} param2 - Options.
			 * @param {boolean} param2.parseHTML - Indique si le contenu doit être analysé comme HTML.
			 * @param {string} param2.position - La position du tooltip.
			 * @param {number} param2.offset - Le décalage du tooltip.
			 * @param {boolean} param2.styled - Indique si le tooltip doit être stylisé.
			 * @param {HTMLElement | string} param2.appendTo - L'élément auquel le tooltip sera ajouté.
			 * @returns {Tooltip | Array<Tooltip>} - L'objet tooltip.
			 */
			show(target, content, options = {}) {
				// Si l'élément cible est une chaîne de caractères, on récupère les éléments correspondant et
				// on affiche le tooltip pour chaque élément.
				if (typeof target === 'string') {
					return Array.from(document.querySelectorAll(target)).map((el) =>
						this.show(el, content, options)
					);
				}

				// Si l'élément de destination est une chaîne de caractères, on récupère l'élément correspondant.
				if (options.appendTo && typeof options.appendTo === 'string') {
					options.appendTo = document.querySelector(options.appendTo);
				}

				// Si l'élément cible a déjà un tooltip, on ne crée pas de nouveau tooltip.
				if (target.hasAttribute('data-tooltip')) {
					return;
				}

				return createTooltip({ content, target, ...options });
			}
		};
	});
