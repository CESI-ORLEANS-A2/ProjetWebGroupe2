/// <reference path="../../static/js/base.js" />

app &&
	app.registerComponent('button', function(component) {
		const button = component.element;

		button.addEventListener('click', function(event) {
			if (button.hasAttribute('disabled')) {
				event.preventDefault();
				event.stopPropagation();
			}

			if (button.hasAttribute('type') && button.getAttribute('type') === 'submit') {
				const form = button.closest('form');
				if (form) {
					form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
				}
			}
		})
	}, function (component, args) {
		return app.createElement(
			'div',
			{
				class: 'c-button',
				id: args.id
			},
			(args.icon || args.iconBefore) && app.createIcon(args.icon || args.iconBefore),
			app.createElement('span', {}, args.text),
			args.iconAfter && app.createIcon(args.iconAfter)
		);
	});
