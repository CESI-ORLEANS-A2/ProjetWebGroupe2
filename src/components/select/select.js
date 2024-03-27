/// <reference path="../../static/js/base.js" />

app &&
	app.registerComponent(
		'select',
		function ({ element: select, data }) {
			data.selectedOptions = [];
			data.selectedOptionsBeforeChange = [];
			data.selectedOptionsBeforeInput = [];

			select.addEventListener('click', (e) => {
				if (e.target.closest('.c-select_options')) return;
				e.preventDefault();

				let isOpened = select.getAttribute('data-opened');
				let close = function () {
					select.removeAttribute('data-opened');
					document.body.removeEventListener('click', callback);

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

					data.selectedOptionsBeforeChange = structuredClone(data.selectedOptions);
				};
				let callback = function (event) {
					if (
						event.target.closest('.c-select_options') !==
						select.querySelector('.c-select_options')
					) {
						close();
					}
				};

				if (isOpened === 'true') {
					close();
				} else {
					const options = select.querySelector('.c-select_options');
					options.style.display = 'flex';
					const bounding = options.getBoundingClientRect();
					options.style.display = '';
					if (bounding.right > window.innerWidth) {
						if (bounding.bottom > window.innerHeight) {
							options.style.top = 'auto';
							options.style.bottom = '0';
						}
						options.style.left = 'auto';
						options.style.right = 'calc(100% + .5rem)';
					} else if (bounding.bottom > window.innerHeight) {
						options.style.top = 'auto';
						options.style.bottom = 'calc(100% + .5rem)';
					}

					select.setAttribute('data-opened', '');
					document.body.addEventListener('click', callback, true);
				}
			});

			for (let option of select.querySelectorAll('.c-select_option')) {
				if (option.hasAttribute('data-selected')) {
					data.selectedOptions.push(option.getAttribute('data-value'));
				}

				option.addEventListener('click', (clickEvent) => {
					clickEvent.preventDefault();
					if (option.hasAttribute('data-selected')) {
						if (
							data.selectedOptions.length === 1
								? select.hasAttribute('data-allow-empty')
								: true
						) {
							if (!select.hasAttribute('data-multiple')) {
								select.removeAttribute('data-selected');
								data.selectedOptions = [];
							} else {
								select.dataset.selected = select.dataset.selected
									.split(',')
									.filter((value) => value !== option.getAttribute('data-value'))
									.join(',');
								data.selectedOptions = data.selectedOptions.filter(
									(value) => value !== option.getAttribute('data-value')
								);
							}
							option.removeAttribute('data-selected');
						}
					} else {
						if (!select.hasAttribute('data-multiple')) {
							select.querySelectorAll('.c-select_option').forEach((opt) => {
								opt.removeAttribute('data-selected');
							});
							select.dataset.selected = option.getAttribute('data-value');
							data.selectedOptions = [option.getAttribute('data-value')];
						} else {
							select.dataset.selected = select.dataset.selected
								.split(',')
								.concat(option.getAttribute('data-value'));
							data.selectedOptions.push(option.getAttribute('data-value'));
						}
						option.setAttribute('data-selected', null);
					}

					app.dispatchEvent(select, 'input', {
						oldSelected: data.selectedOptionsBeforeInput,
						selected: data.selectedOptions
					});

					data.selectedOptionsBeforeInput = structuredClone(data.selectedOptions);

					const current = select.querySelector('.c-select_current');
					if (current)
						current.innerText = data.selectedOptions
							.map(
								(optionId) =>
									select.querySelector(`[data-value="${optionId}"]`).innerText
							)
							.join(', ');
				});
			}

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
					dataMultiple: args.multiple
				},
				(args.icon || args.iconBefore) && app.createIcon(args.icon || args.iconBefore),
				args.prefix && app.createElement('span', { class: 'c-select_prefix' }, args.prefix),
				app.createIcon('arrow_drop_down'),
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
	);
