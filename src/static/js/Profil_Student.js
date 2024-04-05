app.onload(function () {
	const $editButton = document.getElementById('update');

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
						// 	{
						// 		style: {
						// 			display: 'flex',
						// 			flexDirection: 'column'
						// 		}
						// 	},
						// 	app.createElement('label', {}, "Nom d'utilisateur"),
						// 	app.createElement(
						// 		'div',
						// 		{
						// 			style: {
						// 				display: 'flex',
						// 				border: '3px solid #85D3FF',
						// 				borderRadius: '10px',
						// 				overflow: 'hidden',
						// 				height: '40px'
						// 			}
						// 		},
						// 		app.createElement(
						// 			'img',
						// 			{
						// 				src: '//static.projet-web.fr/public/img/users.svg'
						// 			},
						// 			'Titre'
						// 		),

						// 		app.createElement('input', {
						// 			Placeholder: "Nom d'utilisateur",
						// 			class: 'PopUpInput',
						// 			style: {
						// 				border: 'none'
						// 			}
						// 		})
						// 	)
						// ),
						// app.createElement(
						// 	'div',
						// 	{
						// 		style: {
						// 			display: 'flex',
						// 			flexDirection: 'column'
						// 		}
						// 	},
						// 	app.createElement('label', {}, 'Mot de passe'),
						// 	app.createElement(
						// 		'div',
						// 		{
						// 			style: {
						// 				display: 'flex',
						// 				border: '3px solid #85D3FF',
						// 				borderRadius: '10px',
						// 				overflow: 'hidden',
						// 				height: '40px'
						// 			}
						// 		},
						// 		app.createElement(
						// 			'img',
						// 			{
						// 				src: '//static.projet-web.fr/public/img/key.svg'
						// 			},
						// 			'Titre'
						// 		),

						// 		app.createElement(
						// 			'input',
						// 			{
						// 				Placeholder: 'Mot de passe',
						// 				class: 'PopUpInput',
						// 				style: {
						// 					border: 'none'
						// 				}
						// 			},
						// 			'Titre'
						// 		)
						// 	)
						// ),
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
							close();
						}
					}
				]
			})
			.show();
	});
});
