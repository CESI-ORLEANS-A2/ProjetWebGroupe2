/// <reference path="../../static/js/base.js" />

app &&
	app.registerComponent(
		'textarea',
		function ({ element: textarea, data }) {
			const input = textarea.querySelector('textarea') || textarea.querySelector('input');

			/* Cible le champ de texte lorsque le composant est cliqué */
			textarea.addEventListener('click', (event) => {
				if (event.target.tagName === 'TEXTAREA' || event.target.tagName === 'INPUT') return;
				input.focus();
			});

			// Supprime les espaces inutiles
			input.addEventListener('change', (/** @type {InputEvent} */ event) => {
				// 	app.dispatchEvent(textarea, 'input', { value: event.target.value, origin: event });
				data.value = event.target.value.trim();
				// 	event.stopPropagation();
			});

			// Envoie le formulaire lorsque la touche "Entrée" est enfoncée
			input.addEventListener('keydown', (/** @type {KeyboardEvent} */ event) => {
				if (
					event.key === 'Enter' &&
					!data.multiline &&
					!event.shiftKey &&
					!event.ctrlKey &&
					!event.altKey &&
					!event.metaKey &&
					data.value.trim().length > 3
				) {
					const form = textarea.closest('form');
					if (form) {
						form.dispatchEvent(
							new Event('submit', { bubbles: true, cancelable: true })
						);
					}
				}
			});
			// input.addEventListener('change', (event) => {
			// 	app.dispatchEvent(textarea, 'change', { value: event.target.value, origin: event });
			// 	event.stopPropagation();
			// });

			// app.watchVariable(data, 'value', (prop, oldValue, newValue) => {
			// 	console.log(data);
			// });
		},
		function (component, args) {
			if (!["text", "password", "email"].includes(args.type)) {
				args.type = "text";
			}
			
			return app.createElement(
				'div',
				{ class: 'c-textarea', id: args.id },
				args.label && app.createElement('label', {}, args.label),
				app.createElement(
					'div',
					{ class: 'c-textarea_box' },
					args.icon &&
						app.createElement(
							'div',
							{ class: 'c-textarea_icon' },
							app.createIcon(args.icon)
						),
					args.multiline
						? app.createElement(
								'textarea',
								{
									class: 'c-textarea_input',
									name: args.name,
									placeholder: args.placeholder,
									required: args.required
								},
								args.value
						  )
						: app.createElement('input', {
								class: 'c-textarea_input',
								name: args.name,
								placeholder: args.placeholder,
								value: args.value,
								required: args.required,
								type: args.type
						  })
				)
			);
		}
	);
