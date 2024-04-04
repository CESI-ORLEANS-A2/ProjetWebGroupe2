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
const $type = document.querySelector('.c-select#type');
const $skills = document.querySelector('.c-select#skills');
const $studyLevel = document.querySelector('.c-select#study-level');

/**
 * Rendre un élément en fonction de son type.
 *
 * @TODO Remplacer les éléments factices par des éléments réels.
 *
 * @param {Object} item - L'élément à rendre.
 * @returns {HTMLElement} L'élément rendu.
 */
function renderItem(item) {
	let link, icon;
	switch (item.Type) {
		case 'offer':
			link = `/offer?id=${item.ID}`;
			icon = 'work';
			break;
		case 'company':
			link = `/company?id=${item.ID}`;
			icon = 'apartment';
			break;
		case 'student':
			link = `/student?id=${item.ID}`;
			icon = 'person';
			break;
		case 'pilote':
			link = `/pilote?id=${item.ID}`;
			icon = 'school';
			break;
		default:
			link = '#';
			break;
	}
	return app.createElement(
		'a',
		{
			class: 'card',
			href: link
		},
		app.createElement(
			'div',
			{
				class: 'icon'
			},
			app.createIcon(icon)
		),
		app.createElement('div', { class: 'name' }, item.Title),
		app.createElement(
			'div',
			{
				class: 'description'
			},
			item.Description
		),
		app.createElement(
			'div',
			{
				class: 'published'
			},
			item.Creation_Date
		)
	);
}

// Exécuter le code lorsque le DOM est chargé et que les modules ont fini de charger
app.onload(function () {
	// Mettre à jour les résultats de la recherche en fonction de la page actuelle
	$paginator.__component.onUpdate(async function (data) {
		// Effectuer une requête pour obtenir les résultats de la recherche
		const response = await app.request.get('/api/search', {
			query: {
				limit: data.shownCount,
				offset: (data.current - 1) * data.shownCount,
				query: $searchInput.__component.data.value || '',
				location: $locationInput.__component.data.value || '',
				type: $type.__component.data.selectedOptions[0] || '',
				skills: $skills.__component.data.selectedOptions || '',
				studyLevel: $studyLevel.__component.data.selectedOptions || ''
			}
		});

		// Mettre à jour les statistiques de la recherche
		$resultCount.textContent = response.body.match_count;
		$resultTotal.textContent = response.body.total_count;

		// Mettre à jour les résultats de la recherche
		return response.body.data.map(renderItem);
	});

	// Mettre à jour le nombre total de résultats au chargement de la page
	app.request
		.get('/api/search', {
			query: {
				limit: 0,
				offset: 0
			}
		})
		.then(async function (response) {
			$paginator.__component.setCount(response.body.total_count);
		});

	// Mettre à jour les résultats de la recherche en fonction de la recherche
	$form.addEventListener('submit', function (event) {
		event.preventDefault();
		event.stopPropagation();

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
					query: $searchInput.__component.data.value || '',
					location: $locationInput.__component.data.value || '',
					type: $type.__component.data.selectedOptions[0] || '',
					skills: $skills.__component.data.selectedOptions || '',
					studyLevel: $studyLevel.__component.data.selectedOptions || ''
				}
			})
			.then(async function (response) {
				// Mettre à jour les statistiques de la recherche
				$resultCount.textContent = response.body.match_count;
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
	(t = $searchInput.querySelector('input, textarea')) && (t.value = '');

	$locationInput.__component.data.value = '';
	(t = $locationInput.querySelector('input, textarea')) && (t.value = '');

	$paginator.__component.go.first();

	$form.submit();
});
