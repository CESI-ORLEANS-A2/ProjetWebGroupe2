/// <reference path="../../static/js/base.js" />

app.tooltip
	? app.registerComponent(
			'select',
			function ({ element: select, data }) {
				// Contient la liste des options sélectionnées
				data.selectedOptions = [];
				// Contient la liste des options sélectionnées avant le dernier changement
				// Permet de comparer les deux listes et de déclencher l'événement "change"
				data.selectedOptionsBeforeChange = [];
				// Contient la liste des options sélectionnées avant le dernier input
				// Permet de comparer les deux listes et de déclencher l'événement "input"
				data.selectedOptionsBeforeInput = [];

				/**
				 * Ferme la liste déroulante.
				 * @returns {void}
				 */
				function close() {
					// On retire l'attribut "data-opened" pour cacher la liste déroulante
					select.removeAttribute('data-opened');
					// On retire l'écouteur d'événement sur le document,
					// dans un souci de performance
					document.body.removeEventListener('click', documentClickHandler);

					// On retire le tooltip de la liste déroulante
					// et on le remet dans le DOM
					if (select.__tooltip) {
						select.append(select.__tooltip.content);
						select.__tooltip.remove();
					}

					// On vérifie si la liste déroulante a changé
					// et on déclenche l'événement "change" si c'est le cas
					if (
						data.selectedOptionsBeforeChange.length !== data.selectedOptions.length ||
						!data.selectedOptionsBeforeChange.every((value) =>
							data.selectedOptions.includes(value)
						)
					)
						app.dispatchEvent(select, 'change', {
							oldSelected: data.selectedOptionsBeforeChange,
							selected: data.selectedOptions
						});

					// On met à jour les valeurs de la liste déroulante
					data.selectedOptionsBeforeChange = structuredClone(data.selectedOptions);
				}

				/**
				 * Gère la fermeture de la liste déroulante.
				 * @param {MouseEvent} event - L'événement de clic.
				 * @returns {void}
				 */
				function documentClickHandler(event) {
					// Si l'élément cliqué n'est pas dans la liste déroulante,
					// on ferme la liste déroulante
					if (
						select.hasAttribute('data-opened') &&
						event.target.closest('.c-select_options') !==
							select.querySelector('.c-select_options')
					) {
						close();
						// Cette ligne permet d'empêcher la propagation de l'événement
						// et d'éviter que le clic ne soit pris en compte par d'autres éléments
						// Elle est commentée car elle empèche d'ouvrir une liste déroulante si
						// une autre est déjà ouverte
						// Par exemple : si on ouvre une liste déroulante et qu'on clique sur une autre,
						// la première se ferme et la seconde ne s'ouvre pas
						// event.stopPropagation();
					}
				}

				/**
				 * Gère le clic sur une puce.
				 * Si une puce est cliquée, elle est retirée de la liste des éléments sélectionnés.
				 * @param {MouseEvent} event - L'événement de clic.
				 * @returns {void}
				 */
				function chipClickHandler(event) {
					// On récupère la puce cliquée
					const $chip = event.target.closest('.c-select_chip');
					// On récupère la valeur de la puce
					const value = $chip.dataset.value;

					// On retire la valeur de la liste des éléments sélectionnés
					removeOne(value);
					// On actualise l'affichage
					displaySelected();
					// On actualise l'option correspondante dans le menu déroulant
					select
						.querySelector(`[data-value="${value}"]`)
						.removeAttribute('data-selected');
				}

				/**
				 * Affiche la liste des options dans un menu déroulant.
				 * Le menu déroulant est affiché sous le composant (par défaut) et est sous forme de tooltip.
				 * @returns {void}
				 */
				function showOptions() {
					// On récupère la liste des options
					const options = select.querySelector('.c-select_options');
					if (options) {
						// On crée un tooltip avec les options.
						app.tooltip.show(select, options, {
							position: 'bottom',
							offset: 5,
							styled: false,
							appendTo: select,
							align: 'border',
							fullWidth: true,
							delay: 0
						});
					}

					// On ajoute l'attribut "data-opened" pour afficher la liste déroulante
					select.setAttribute('data-opened', '');
					// On ajoute un écouteur d'événement sur le document pour fermer la liste déroulante
					// lorsqu'on clique en dehors
					document.body.addEventListener('click', documentClickHandler, true);
				}

				/**
				 * Ajoute une option à la liste des éléments sélectionnés.
				 * @param {string} value - La valeur de l'option à ajouter.
				 */
				function addOne(value) {
					select.dataset.selected = select.dataset.selected
						.split(',')
						.concat(value)
						.join(',');
					data.selectedOptions.push(value);
				}

				/**
				 * Ajoute toutes les options à la liste des éléments sélectionnés.
				 * @returns {void}
				 */
				function addAll() {
					select.dataset.selected = data.options.map((option) => option.value).join(',');
					data.selectedOptions = data.options.map((option) => option.value);
				}

				/**
				 * Retire toutes les options de la liste des éléments sélectionnés sauf une.
				 * @param {string} value - La valeur de l'option à conserver.
				 * @returns {void}
				 */
				function removeAllButOne(value) {
					select.dataset.selected = value;
					data.selectedOptions = [value];
				}

				/**
				 * Retire une option de la liste des éléments sélectionnés.
				 * @param {string} value - La valeur de l'option à retirer.
				 * @returns {void}
				 */
				function removeOne(value) {
					select.dataset.selected = select.dataset.selected
						.split(',')
						.filter((v) => v !== value)
						.join(',');
					data.selectedOptions = data.selectedOptions.filter((v) => v !== value);
				}

				/**
				 * Retire toutes les options de la liste des éléments sélectionnés.
				 * @returns {void}
				 */
				function removeAll() {
					select.dataset.selected = '';
					data.selectedOptions = [];
				}

				/**
				 * Affiche les puces correspondant aux éléments sélectionnés.
				 * @returns {void}
				 */
				function displayChips() {
					const current = select.querySelector('.c-select_current');
					if (current) {
						// On supprime les éléments actuels
						current.innerHTML = '';
						// On affiche les éléments sélectionnés sous forme de puces
						// Exemple :
						//     /-----------------\  /-----------------\
						//     | Option 1      x |  | Option 2      x |
						//     \-----------------/  \-----------------/
						// Ces puces sont cliquables et permettent de retirer une option de la liste.
						current.append(
							...data.selectedOptions.map((optionId) =>
								app.createElement(
									'div',
									{
										class: 'c-select_chip',
										dataValue: optionId
									},
									select.querySelector(`[data-value="${optionId}"]`).innerText,
									app.createIcon('close')
								)
							)
						);
					}
				}

				/**
				 * Affiche le texte correspondant aux éléments sélectionnés.
				 * @returns {void}
				 */
				function displayText() {
					const current = select.querySelector('.c-select_current');
					if (current) {
						// On affiche les éléments sélectionnés sous forme de texte : "Option 1, Option 2, Option 3"
						current.innerText = data.selectedOptions
							.map(
								(optionId) =>
									select.querySelector(`[data-value="${optionId}"]`).innerText
							)
							.join(', ');
					}
				}

				/**
				 * Affiche les éléments sélectionnés sous la forme de puces ou de texte
				 * en fonction de l'attribut "data-show-chips".
				 * @returns {void}
				 */
				function displaySelected() {
					if (select.dataset.showChips === 'true') {
						displayChips();
					} else {
						displayText();
					}
				}

				/**
				 * Gère le clic sur une option.
				 * @param {HTMLElement} option - L'option cliquée.
				 * @param {MouseEvent} clickEvent - L'événement de clic.
				 * @returns {void}
				 */
				function optionClickHandler(option, clickEvent) {
					clickEvent.preventDefault();

					// Il y a 4 cas possibles :
					// 1. L'option est sélectionnée et la liste déroulante autorise plusieurs sélections
					//   => On retire l'option de la liste des éléments sélectionnés
					// 2. L'option est sélectionnée et la liste déroulante n'autorise pas plusieurs sélections
					//   => On retire toutes les options de la liste des éléments sélectionnés
					// 3. L'option n'est pas sélectionnée et la liste déroulante autorise plusieurs sélections
					//   => On ajoute l'option à la liste des éléments sélectionnés
					// 4. L'option n'est pas sélectionnée et la liste déroulante n'autorise pas plusieurs sélections
					//   => On retire toutes les options de la liste des éléments sélectionnés sauf celle-ci

					// On détermine le cas en fonction des attributs "data-selected" et "data-multiple"
					// de la liste déroulante et de l'option cliquée. Ces attributs sont des booléens
					// ce qui permet de les placer dans un nombre binaire à 2 chiffres :
					//    - 00 (0) : l'option n'est pas sélectionnée et la liste déroulante n'autorise pas plusieurs sélections
					//    - 01 (1) : l'option est sélectionnée et la liste déroulante n'autorise pas plusieurs sélections
					//    - 10 (2) : l'option n'est pas sélectionnée et la liste déroulante autorise plusieurs sélections
					//    - 11 (3) : l'option est sélectionnée et la liste déroulante autorise plusieurs sélections
					// On peut alors utiliser un switch pour gérer les 4 cas.
					switch (
						(option.hasAttribute('data-selected') << 0) |
						(select.hasAttribute('data-multiple') << 1) // 11 => isSelected: true, isMultiple: true
					) {
						case 3: {
							// Si la liste déroulante n'autorise pas 0 élément sélectionné
							// et qu'il n'y a qu'une seule option sélectionnée
							// on ne peut pas la retirer
							if (
								data.selectedOptions.length === 1 &&
								!select.hasAttribute('data-allow-empty')
							)
								return;

							// On retire l'option de la liste des éléments sélectionnés
							removeOne(option.getAttribute('data-value'));

							// On retire l'attribut "data-selected" de l'option
							option.removeAttribute('data-selected');
							break;
						} // 01 => isSelected: true, isMultiple: false
						case 1: {
							// On retire toutes les options de la liste des éléments sélectionnés
							removeAll();

							// On retire l'attribut "data-selected" de l'option
							option.removeAttribute('data-selected');
							break;
						} // 10 => isSelected: false, isMultiple: true
						case 2: {
							// On ajoute l'option à la liste des éléments sélectionnés
							addOne(option.getAttribute('data-value'));

							// On ajoute l'attribut "data-selected" à l'option
							option.setAttribute('data-selected', null);
							break;
						} // 00 => isSelected: false, isMultiple: false
						case 0: {
							// On retire l'attribut "data-selected" de toutes les options
							select.querySelectorAll('.c-select_option').forEach((opt) => {
								opt.removeAttribute('data-selected');
							});
							// On retire toutes les options de la liste des éléments sélectionnés sauf celle-ci
							removeAllButOne(option.getAttribute('data-value'));

							// On ajoute l'attribut "data-selected" à l'option
							option.setAttribute('data-selected', null);
							break;
						}
					}

					// On déclenche l'événement "input" si la liste des éléments sélectionnés a changé
					// depuis le dernier input
					// Cet évènement contient les éléments sélectionnés avant et après le changement
					app.dispatchEvent(select, 'input', {
						oldSelected: data.selectedOptionsBeforeInput,
						selected: data.selectedOptions
					});

					// On met à jour les valeurs de la liste déroulante
					data.selectedOptionsBeforeInput = structuredClone(data.selectedOptions);

					// On actualise l'affichage
					displaySelected();
				}

				select.addEventListener('click', (e) => {
					// Si on clique sur une option, on ne fait rien
					if (e.target.closest('.c-select_options')) return;
					// On empêche le clic de se propager
					e.preventDefault();

					// Si on clique sur une puce, on retire l'option de la liste des éléments sélectionnés
					if (e.target.closest('.c-select_chip')) {
						chipClickHandler(e);
						e.preventDefault();
						e.stopPropagation();
						return;
					}

					// On récupère l'attribut "data-opened" qui indique si la liste déroulante est ouverte
					let isOpened = select.getAttribute('data-opened');

					// Si la liste déroulante est ouverte, on la ferme
					// Sinon, on l'ouvre
					if (isOpened === 'true') {
						close();
					} else {
						showOptions();
					}
				});

				// Pour chaque option de la liste déroulante
				for (let option of select.querySelectorAll('.c-select_option')) {
					// On ajoute l'option à la liste des options si elle est sélectionnée
					if (option.hasAttribute('data-selected')) {
						data.selectedOptions.push(option.getAttribute('data-value'));
					}

					// On ajoute un écouteur d'événement sur le clic sur chaque option
					option.addEventListener('click', optionClickHandler.bind(null, option));
				}

				// On met à jour les valeurs de la liste déroulante
				select.dataset.selected = data.selectedOptions;
				data.selectedOptionsBeforeChange = structuredClone(data.selectedOptions);
				data.selectedOptionsBeforeInput = structuredClone(data.selectedOptions);
			},
			function (component, args) {
				return app.createElement(
					'div',
					{
						class: 'c-select',
						id: args.id,
						dataMultiple: args.multiple,
						dataShowChips: args.showChips
					},
					app.createElement(
						'div',
						{ class: 'c-select_button' },
						(args.icon || args.iconBefore) &&
							app.createIcon(args.icon || args.iconBefore),
						args.prefix &&
							app.createElement('span', { class: 'c-select_prefix' }, args.prefix),
						app.createElement(
							'div',
							{ class: 'c-select_current' },
							...(args.options instanceof Array
								? args.showChips
									? args.options
											.filter((o) => o.selected)
											.map((option) =>
												app.createElement(
													'div',
													{
														class: 'c-select_chip',
														dataValue: option.value
													},
													option.text,
													app.createIcon('close')
												)
											)
									: args.options.filter((o) => o.selected).join(', ')
								: [])
						),
						app.createIcon('arrow_drop_down')
					),
					app.createElement(
						'div',
						{ class: 'c-select_options' },
						...(args.options instanceof Array
							? args.options.map((option) =>
									app.createElement(
										'div',
										{
											class: 'c-select_option',
											dataValue: option.value,
											dataSelected: option.selected,
											dataDisabled: option.disabled
										},
										option.text
									)
							  )
							: [])
					)
				);
			}
	  )
	: console.error('Tooltip component is required for the select component to work.');
