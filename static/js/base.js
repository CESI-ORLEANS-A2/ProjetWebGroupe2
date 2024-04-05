/// <reference path="./core/app.js" />

(function () {
	// Ajoute un effet de ripple sur les éléments avec l'attribut `c-ripple`.
	document.addEventListener(
		'mousedown',
		function (event) {
			/** @type {HTMLElement} */
			const target = event.target.closest('[c-ripple]');
			if (target) {
				// Créer un élément pour le ripple.
				const ripple = app.createElement('div', {
					class: 'c-ripple'
				});

				// Ajouter le ripple à l'élément.
				target.prepend(ripple);

				// Calculer la position et la taille du ripple à partir de la position
				// de la souris et des dimensions et position de l'élément.
				const size = Math.max(target.offsetWidth, target.offsetHeight);
				const x = event.clientX - target.getBoundingClientRect().left - size / 2;
				const y = event.clientY - target.getBoundingClientRect().top - size / 2;

				// Appliquer les valeurs calculées au ripple.
				ripple.style.top = `${y}px`;
				ripple.style.left = `${x}px`;
				ripple.style.width = `${size}px`;
				ripple.style.height = `${size}px`;

				// Supprimer le ripple après l'animation.
				setTimeout(() => ripple.remove(), 600);
			}

			return true;
		}
	);

	document.addEventListener(
		'mouseenter',
		function (event) {
			const target = event.target?.closest?.('[c-tooltip]');
			if (target && !target.__tooltip) {
				const tooltip = app.tooltip.show(target, target.getAttribute('c-tooltip'), {
					position: target.getAttribute('c-tooltip-position') || undefined,
					align: target.getAttribute('c-tooltip-align') || undefined,
					offset: target.getAttribute('c-tooltip-offset') || undefined,
					delay: target.getAttribute('c-tooltip-delay') || undefined
				});

				target.addEventListener(
					'mouseleave',
					function (outEvent) {
						tooltip.remove();
					},
					{ once: true }
				);
			}
		},
		true
	);
})();
