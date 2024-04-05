/// <reference path="../../static/js/base.js" />

app &&
	app.registerComponent(
		'paginator',
		function (component) {
			const element = component.element;
			const data = component.data;

			const callbacks = {
				onUpdate: null
			};

			const container = element.querySelector('.c-paginator_container');
			const shownCountSelect = element.querySelector('.c-paginator_footer > .c-select');
			const firstPage = element.querySelector('.c-paginator_button-first');
			const previousPage = element.querySelector('.c-paginator_button-previous');
			const firstNumber = element.querySelector('.c-paginator_page-number-first');
			const dotsPrevious = element.querySelector('.c-paginator_page-dots-previous');
			const previousNumber = element.querySelector('.c-paginator_page-number-previous');
			const currentNumber = element.querySelector('.c-paginator_page-number-current');
			const nextNumber = element.querySelector('.c-paginator_page-number-next');
			const dotsNext = element.querySelector('.c-paginator_page-dots-next');
			const lastNumber = element.querySelector('.c-paginator_page-number-last');
			const nextPage = element.querySelector('.c-paginator_button-next');
			const lastPage = element.querySelector('.c-paginator_button-last');

			data.current = Number(element.dataset.current) || 1;
			data.shownCount = Number(shownCountSelect?.__component.data.selectedOptions[0]) || 10;
			data.count = Number(element.dataset.count) || 1;
			data.max = Math.ceil(data.count / data.shownCount);

			const showSkeleton = () => {
				container.innerHTML = '';
				for (let i = 0; i < data.shownCount; i++) {
					container.appendChild(
						app.createComponent('skeleton', {
							width: '100%',
							height: '7rem',
							styles: ['rounded-lg']
						}).element
					);
				}
			};

			const el = {
				hide: (el) => {
					el.classList.add('hidden');
				},
				show: (el) => {
					el.classList.remove('hidden');
				},
				enable: (el) => {
					el.classList.remove('disabled');
				},
				disable: (el) => {
					el.classList.add('disabled');
				}
			};

			const updateButtons = () => {
				if (data.current <= 1) {
					el.disable(firstPage);
					el.disable(previousPage);
					el.hide(previousNumber);
				} else {
					el.enable(firstPage);
					el.enable(previousPage);
					el.show(previousNumber);
				}
				if (data.current <= 2) {
					el.hide(firstNumber);
					el.hide(dotsPrevious);
				} else {
					el.show(firstNumber);
					el.show(dotsPrevious);
				}

				if (data.current >= data.max) {
					el.disable(nextPage);
					el.disable(lastPage);
					el.hide(nextNumber);
				} else {
					el.enable(nextPage);
					el.enable(lastPage);
					el.show(nextNumber);
				}
				if (data.current >= data.max - 1) {
					el.hide(dotsNext);
					el.hide(lastNumber);
				} else {
					el.show(dotsNext);
					el.show(lastNumber);
				}

				firstNumber.textContent = 1;
				previousNumber.textContent = data.current - 1;
				currentNumber.textContent = data.current;
				nextNumber.textContent = data.current + 1;
				lastNumber.textContent = data.max;
			};

			const update = async () => {
				element.dataset.current = data.current;
				element.dataset.max = data.max;
				element.dataset.shownCount = data.shownCount;
				element.dataset.count = data.count;

				updateButtons();

				if (callbacks.onUpdate) {
					let canceled = false;
					const updateLoaderTimeout = setTimeout(() => {
						showSkeleton();
					}, 1000);
					const updateTimeout = setTimeout(() => {
						app.toast.error('La mise à jour a pris trop de temps');
						canceled = true;
					}, 10000);
					const items = await callbacks.onUpdate.call(element, data);
					clearTimeout(updateLoaderTimeout);
					clearTimeout(updateTimeout);

					if (canceled || !items || !items.length) return;
					container.innerHTML = '';
					items.forEach((item) => item instanceof Node && container.append(item));
				} else showSkeleton();
			};

			const go = {
				first: () => {
					if (data.current > 1) {
						data.current = 1;
						update();
					}
				},
				previous: () => {
					if (data.current > 1) {
						data.current--;
						update();
					}
				},
				next: () => {
					if (data.current < data.max) {
						data.current++;
						update();
					}
				},
				last: () => {
					if (data.current < data.max) {
						data.current = data.max;
						update();
					}
				},
				to: (page) => {
					if (page >= 1 && page <= data.max) {
						data.current = page;
						update();
					}
				}
			};

			firstPage.addEventListener('click', go.first);
			previousPage.addEventListener('click', go.previous);
			firstNumber.addEventListener('click', go.first);
			previousNumber.addEventListener('click', go.previous);
			nextNumber.addEventListener('click', go.next);
			lastNumber.addEventListener('click', go.last);
			nextPage.addEventListener('click', go.next);
			lastPage.addEventListener('click', go.last);
			shownCountSelect?.addEventListener('input', (event) => {
				data.shownCount = Number(event.detail.selected[0]);
				data.max = Math.ceil(data.count / data.shownCount);
				data.current = 1;
				update();
			});

			component.onUpdate = (callback) => {
				callbacks.onUpdate = callback;
			};
			component.setCount = (count) => {
				data.count = count;
				data.max = Math.ceil(count / data.shownCount);
				update();
			};
			component.go = go;
			component.showLoading = showSkeleton;
			component.update = (count, items) => {
				data.count = count;
				data.max = Math.ceil(count / data.shownCount);

				element.dataset.count = count;
				element.dataset.max = data.max;

				updateButtons();

				container.innerHTML = '';
				items.forEach((item) => item instanceof Node && container.append(item));
			};

			updateButtons();
		},
		function (component, args) {
			args.showCount ||= 10;

			return app.createElement(
				'div',
				{
					class: 'c-paginator'
				},
				app.createElement(
					'div',
					{
						class: 'c-paginator_container'
					},
					...Array.from({ length: args.max }, (_, i) =>
						app.createElement('div', {
							class: 'c-skeleton rounded-lg',
							style: '--skeleton-width:100%;--skeleton-height:7rem;'
						})
					)
				),
				app.createElement(
					'div',
					{
						class: 'c-paginator_footer'
					},
					app.createElement(
						'div',
						{
							class: 'c-paginator_nav'
						},
						app.createElement(
							'div',
							{
								class: 'c-paginator_button c-paginator_button-first',
								cRipple: true
							},
							app.createComponent('icon', { __icon: 'first_page' }).element
						),
						app.createElement(
							'div',
							{
								class: 'c-paginator_button c-paginator_button-previous',
								cRipple: true
							},
							app.createComponent('icon', { __icon: 'chevron_left' }).element
						),
						app.createElement(
							'div',
							{
								class: 'c-paginator_page-numbers-container'
							},
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-number c-paginator_page-number-first',
									cRipple: true
								},
								'1'
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-dots c-paginator_page-dots-previous'
								},
								app.createComponent('icon', { __icon: 'more_horiz' }).element
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-number c-paginator_page-number-previous',
									cRipple: true
								},
								'3'
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-number c-paginator_page-number-current',
									cRipple: true
								},
								'4'
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-number c-paginator_page-number-next',
									cRipple: true
								},
								'5'
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-dots c-paginator_page-dots-next'
								},
								app.createComponent('icon', { __icon: 'more_horiz' }).element
							),
							app.createElement(
								'div',
								{
									class: 'c-paginator_page-number c-paginator_page-number-last',
									cRipple: true
								},
								'23'
							)
						),
						app.createElement(
							'div',
							{
								class: 'c-paginator_button c-paginator_button-next',
								cRipple: true
							},
							app.createComponent('icon', { __icon: 'chevron_right' }).element
						),
						app.createElement(
							'div',
							{
								class: 'c-paginator_button c-paginator_button-last',
								cRipple: true
							},
							app.createComponent('icon', { __icon: 'last_page' }).element
						)
					),
					args.shownCountCanChange &&
						app.createComponent('select', {
							prefix: 'Résultats : ',
							options: [
								{ value: 10, text: '10', selected: args.shownCount == 10 },
								{ value: 20, text: '20', selected: args.shownCount == 20 },
								{ value: 50, text: '50', selected: args.shownCount == 50 },
								{ value: 100, text: '100', selected: args.shownCount == 100 }
							]
						})
				)
			);
		}
	);
