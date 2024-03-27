/// <reference path="base.js" />

// Récupère les éléments de la page
const $paginator = document.querySelector('.search-results > .c-paginator');
const $resultCount = document.querySelector('.search-sort .result-count');
const $resultTotal = document.querySelector('.search-sort .result-total');
const $searchInput = document.querySelector('.c-textarea#job');
const $searchButton = document.querySelector('.c-button#submit');
const $locationInput = document.querySelector('.c-textarea#location');
const $clearButton = document.querySelector('.c-button#clear');
const $form = document.querySelector('form.search-header');

/**
 * Rendre un élément en fonction de son type.
 *
 * @TODO Remplacer les éléments factices par des éléments réels.
 *
 * @param {Object} item - L'élément à rendre.
 * @returns {HTMLElement} L'élément rendu.
 */
function renderItem(item) {
	switch (item.type) {
		case 'student':
			return app.createElement(
				'div',
				{
					class: 'student',
					style: {
						height: '7rem',
						width: '100%',
						backgroundColor: 'var(--background-primary-color)',
						marginBottom: '1rem'
					}
				},
				app.createElement('div', {
					style: {
						height: '100%',
						width: '7rem',
						backgroundColor: 'var(--background-secondary-color)',
						borderRadius: '50%',
						marginRight: '1rem',
						float: 'left'
					}
				}),
				app.createElement(
					'div',
					{ class: 'name', style: { marginTop: '1rem' } },
					item.name
				),
				app.createElement('div', { class: 'class' }, item.class),
				app.createElement('div', { class: 'location' }, item.location)
			);
		case 'compagny':
			return app.createElement(
				'div',
				{
					class: 'compagny',
					style: {
						height: '7rem',
						width: '100%',
						backgroundColor: 'var(--background-primary-color)',
						marginBottom: '1rem'
					}
				},
				app.createElement('div', {
					style: {
						height: '100%',
						width: '7rem',
						backgroundColor: 'var(--background-secondary-color)',
						borderRadius: '50%',
						marginRight: '1rem',
						float: 'left'
					}
				}),
				app.createElement(
					'div',
					{ class: 'name', style: { marginTop: '1rem' } },
					item.name
				),
				app.createElement('div', { class: 'description' }, item.description),
				app.createElement('div', { class: 'location' }, item.location)
			);
		case 'offer':
			return app.createElement(
				'div',
				{
					class: 'offer',
					style: {
						height: '7rem',
						width: '100%',
						backgroundColor: 'var(--background-primary-color)',
						marginBottom: '1rem'
					}
				},
				app.createElement('div', {
					style: {
						height: '100%',
						width: '7rem',
						backgroundColor: 'var(--background-secondary-color)',
						borderRadius: '50%',
						marginRight: '1rem',
						float: 'left'
					}
				}),
				app.createElement(
					'div',
					{ class: 'name', style: { marginTop: '1rem' } },
					item.name
				),
				app.createElement('div', { class: 'published' }, item.published_at),
				app.createElement('div', { class: 'location' }, item.location)
			);
	}
}

// Exécuter le code lorsque le DOM est chargé et que les modules ont fini de charger
app.onload(function () {
	// Mettre à jour les résultats de la recherche en fonction de la page actuelle
	$paginator.__component.onUpdate(async function (data) {
		// Effectuer une requête pour obtenir les résultats de la recherche
		const response = await app.request.get('/api/search', {
			query: {
				limit: data.shownCount,
				offset: (data.current - 1) * data.shownCount
			}
		});

		// Mettre à jour les statistiques de la recherche
		$resultCount.textContent = response.body.count;
		$resultTotal.textContent = response.body.total_count;

		// Mettre à jour les résultats de la recherche
		return response.body.data.map(renderItem);
	});

	// Mettre à jour le nombre total de résultats au chargement de la page
	app.request
		.get('/api/search', {
			query: { limit: 0, offset: 0 }
		})
		.then(async function (response) {
			$paginator.__component.setCount(response.body.total_count);
		});

	// Mettre à jour les résultats de la recherche en fonction de la recherche
	$form.addEventListener('submit', function (event) {
		event.preventDefault();
		event.stopPropagation();

		// Récupérer la valeur de la recherche
		let value = $searchInput.__component.data.value || '';
		const location = $locationInput.__component.data.value || '';

		// Ajouter la recherche par localisation si elle est spécifiée
		if (location.length > 3) {
			value += ` location:${location}`;
		}

		// Mettre à jour les résultats de la recherche
		if (value.length === 0) {
			$paginator.__component.go.first();
			return;
		}
		// Ne pas effectuer de recherche si la valeur est trop courte
		if (value.length < 3) return;

		// Revient à la première page et affiche l'indicateur de chargement
		$paginator.__component.data.current = 1;
		$paginator.__component.showLoading();

		// Effectuer la recherche
		app.request
			.get('/api/search', {
				query: {
					limit: $paginator.__component.data.shownCount,
					offset:
						($paginator.__component.data.current - 1) *
						$paginator.__component.data.shownCount,
					query: value
				}
			})
			.then(async function (response) {
				// Mettre à jour les statistiques de la recherche
				$resultCount.textContent = response.body.count;
				$resultTotal.textContent = response.body.total_count;

				// Mettre à jour les résultats de la recherche
				$paginator.__component.update(
					response.body.match_count,
					response.body.data.map(renderItem)
				);
			});
	});
});

// Effacer les champs de recherche
$clearButton.addEventListener('click', function () {
	$searchInput.__component.data.value = '';
	$locationInput.__component.data.value = '';
	$paginator.__component.go.first();
});
