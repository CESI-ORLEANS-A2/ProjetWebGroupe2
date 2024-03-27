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
		},
		// Utiliser la capture pour que le ripple soit créé avant que l'événement ne se propage.
		{ passive: true }
	);
})();
