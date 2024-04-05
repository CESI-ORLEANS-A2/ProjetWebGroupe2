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
					class: "icon"
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
		)
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
						app.createElement(
							'div',
							{
								style: {
									display: 'flex',
									flexDirection: 'column'
								}
							},
							app.createElement('label', {}, "Nom d'utilisateur"),
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										border: '3px solid #85D3FF',
										borderRadius: '10px',
										overflow: 'hidden',
										height: '40px'
									}
								},
								app.createElement(
									'img',
									{
										src: '//static.projet-web.fr/public/img/users.svg'
									},
									'Titre'
								),

								app.createElement('input', {
									Placeholder: "Nom d'utilisateur",
									class: 'PopUpInput',
									style: {
										border: 'none'
									}
								})
							)
						),
						app.createElement(
							'div',
							{
								style: {
									display: 'flex',
									flexDirection: 'column'
								}
							},
							app.createElement('label', {}, 'Mot de passe'),
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										border: '3px solid #85D3FF',
										borderRadius: '10px',
										overflow: 'hidden',
										height: '40px'
									}
								},
								app.createElement(
									'img',
									{
										src: '//static.projet-web.fr/public/img/key.svg'
									},
									'Titre'
								),

								app.createElement(
									'input',
									{
										Placeholder: 'Mot de passe',
										class: 'PopUpInput',
										style: {
											border: 'none'
										}
									},
									'Titre'
								)
							)
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
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										flexDirection: 'column'
									}
								},
								app.createElement('label', {}, 'Prénom'),

								app.createElement(
									'div',
									{
										style: {
											display: 'flex',
											border: '3px solid #85D3FF',
											borderRadius: '10px',
											overflow: 'hidden',
											height: '40px'
										}
									},
									app.createElement(
										'img',
										{
											src: '//static.projet-web.fr/public/img/users.svg'
										},
										'Titre'
									),

									app.createElement(
										'input',
										{
											Placeholder: 'Prénom',
											class: 'PopUpInput',
											style: {
												border: 'none'
											}
										},
										'Titre'
									)
								)
							),
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										flexDirection: 'column'
									}
								},
								app.createElement('label', {}, 'Nom'),
								app.createElement(
									'div',
									{
										style: {
											display: 'flex',
											border: '3px solid #85D3FF',
											borderRadius: '10px',
											overflow: 'hidden',
											height: '40px'
										}
									},
									app.createElement(
										'img',
										{
											src: '//static.projet-web.fr/public/img/users.svg'
										},
										'Titre'
									),
									app.createElement(
										'input',
										{
											Placeholder: 'Nom',
											class: 'PopUpInput',
											style: {
												border: 'none'
											}
										},
										'Titre'
									)
								)
							)
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
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										flexDirection: 'column'
									}
								},
								app.createElement('label', {}, 'Etablissement'),
								app.createElement(
									'div',
									{
										style: {
											display: 'flex',
											border: '3px solid #85D3FF',
											borderRadius: '10px',
											overflow: 'hidden',
											height: '40px'
										}
									},
									app.createElement(
										'img',
										{
											src: '//static.projet-web.fr/public/img/users.svg'
										},
										'Titre'
									),

									app.createElement(
										'input',
										{
											Placeholder: 'Etablissement',
											class: 'PopUpInput',
											style: {
												border: 'none'
											}
										},
										'Titre'
									)
								)
							),
							app.createElement(
								'div',
								{
									style: {
										display: 'flex',
										flexDirection: 'column'
									}
								},
								app.createElement('label', {}, 'Promotion'),
								app.createElement(
									'div',
									{
										style: {
											display: 'flex',
											border: '3px solid #85D3FF',
											borderRadius: '10px',
											overflow: 'hidden',
											height: '40px'
										}
									},
									app.createElement(
										'img',
										{
											src: '//static.projet-web.fr/public/img/users.svg'
										},
										'Titre'
									),
									app.createElement(
										'input',
										{
											Placeholder: 'Promotion',
											class: 'PopUpInput',
											style: {
												border: 'none'
											}
										},
										'Titre'
									)
								)
							)
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
									content: ['Voulez vous vraiment supprimer cet utilisateur ?']
								})
								.then(() => close())
								.catch(() => {});
						}
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
									content: ['Voulez vous vraiment ajouter cet utilisateur ?']
								})
								.then(() => {
									var userData = {
										Firstname: 'John',
										Lastname: 'Doe',
										Account: 123
									};

									// AJAX request
									var xhr = new XMLHttpRequest();
									xhr.open('POST', 'http://www.projet-web.fr/user', true);
									xhr.setRequestHeader('Content-Type', 'application/json');
									xhr.onreadystatechange = function () {
										if (xhr.readyState === 4 && xhr.status === 200) {
											console.log(xhr.responseText);
										}
									};
									xhr.send(JSON.stringify(userData));
									console.log(userData);
								})
								.catch(() => {});
						}
					}
				]
			})
			.show();
	});
});
