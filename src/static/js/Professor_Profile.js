app.onload(function () {
	const $editButton = document.getElementById('edit-button');
	const $paginator = document.querySelector('.c-paginator');

	const params = new URLSearchParams(window.location.search);

	if (!params.has('id')) {
		app.toast.error("Erreur: Aucun pilote spécifié dans l'URL");
		return;
	}

	function renderItem(item) {
		return app.createElement(
			'div',
			{
				class: 'card'
			},
			app.createElement(
				'div',
				{
					class: 'icon'
				},
				app.createIcon('group')
			),
			app.createElement(
				'div',
				{
					class: 'infos'
				},
				item.Name
			)
		);
	}

	fetch('/api/classes?pilote=' + params.get('id'))
		.then((response) => response.json())
		.then((json) => {
			if (json.error) {
				app.toast.error('Une erreur est survenue lors de la récupération des promotions');
				return;
			}

			$paginator.__component.update(json.body.count, json.body.data.map(renderItem));

			$paginator.__component.onUpdate(async function (data) {
				const res = await fetch(
					'/api/classes?pilote=' +
						params.get('id') +
						'&offset=' +
						data.current * data.max +
						'&limit=' +
						data.max
				);
				const json = await res.json();

				return json.body.data.map(renderItem);
			});
		});

	$editButton?.addEventListener('click', function () {
		app.dialog
			.create({
				title: "Modifier l'utilisateur",
				content: [
					app.createElement(
						'div',
						{
							id: 'container',
							style: {
								display: 'flex',
								flexDirection: 'column',
								gap: '20px',
								fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
							}
						},
						app.createComponent(
							'textarea',
							{
								placeholder: "Nom d'utilisateur",
								label: "Nom d'utilisateur",
								icon: 'person'
							},
							{}
						).element,
						app.createComponent(
							'textarea',
							{
								placeholder: 'Mot de passe',
								label: 'Mot de passe',
								icon: 'lock'
							},
							{}
						).element,
						app.createElement(
							'div',
							{
								style: {
									display: 'flex',
									flexDirection: 'row',
									gap: '20px'
								}
							},
							app.createComponent(
								'textarea',
								{
									placeholder: 'Prénom',
									label: 'Prénom',
									icon: 'person'
								},
								{}
							).element,
							app.createComponent(
								'textarea',
								{
									placeholder: 'Nom',
									label: 'Nom',
									icon: 'person'
								},
								{}
							).element,
						),

						app.createElement(
							'div',
							{
								style: {
									display: 'flex',
									flexDirection: 'row',
									gap: '20px'
								}
							},
							app.createComponent(
								'textarea',
								{
									placeholder: 'Établissement',
									label: 'Établissement',
									icon: 'apartment'
								},
								{}
							).element,
							app.createComponent(
								'textarea',
								{
									placeholder: 'Promotion',
									label: 'Promotion',
									icon: 'group'
								},
								{}
							).element,
						)
					)
				],
				buttons: [
					{
						text: 'Supprimer le compte',
						color: '#FF6262',
						filled: true,
						action: (close) => {
							app.dialog
								.confirm({
									title: 'Supprimer un utilisateur',
									content: ['Voulez vous vraiment supprimer cet utilisateur ?'],
									onConfirm() {
										close();
									}
								})
								.show();
						},
						align: 'left'
					},

					{ text: 'Annuler', action: 'close' },
					{
						text: 'Valider',
						color: 'var(--color-blue-600)',
						filled: true,
						action: (close) => {
							app.dialog
								.confirm({
									title: 'Ajouter un utilisateur',
									content: ['Voulez vous vraiment ajouter cet utilisateur ?'],
									onConfirm: () => {
										var userData = {
											Firstname: 'John',
											Lastname: 'Doe',
											Account: 123
										};

										// AJAX request
										var xhr = new XMLHttpRequest();
										xhr.open('POST', 'http://projet-web.fr/user', true);
										xhr.setRequestHeader('Content-Type', 'application/json');
										xhr.onreadystatechange = function () {
											if (xhr.readyState === 4 && xhr.status === 200) {
												console.log(xhr.responseText);
											}
										};
										xhr.send(JSON.stringify(userData));
										console.log(userData);
									}
								})
								.show();
						}
					}
				]
			})
			.show();
	});
});
