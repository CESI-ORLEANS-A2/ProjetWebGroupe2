document.getElementById('update').addEventListener('click', function () {
	app.dialog.create({
		title: "Modifier l'utilisateur",
		content: [
			app.createElement('div', {
				id: 'container',
				style: {
					display: 'flex',
					flexDirection: 'column',
					gap: '20px',
					fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
				}
			},
				app.createElement('div', {
					style: {
						display: 'flex',
						flexDirection: 'column'
					}
				},
					app.createElement('label', {}, "Nom d'utilisateur"),
					app.createElement('div', {
						style: {
							display: 'flex',
							border: '3px solid #85D3FF',
							borderRadius: '10px',
							overflow: 'hidden',
							height: '40px'
						}
					},
						app.createElement('img', {
							src: '//static.projet-web.fr/public/img/users.svg'
						}, 'Titre'),

						app.createElement('input', {
							Placeholder: "Nom d'utilisateur",
							class: 'PopUpInput',
							style: {
								border: 'none'
							}
						},))),
				app.createElement('div', {
					style: {
						display: 'flex',
						flexDirection: 'column'
					}
				},
					app.createElement('label', {}, "Mot de passe"),
					app.createElement('div', {
						style: {
							display: 'flex',
							border: '3px solid #85D3FF',
							borderRadius: '10px',
							overflow: 'hidden',
							height: '40px'
						}
					},
						app.createElement('img', {
							src: '//static.projet-web.fr/public/img/key.svg'
						}, 'Titre'),

						app.createElement('input', {
							Placeholder: 'Mot de passe',
							class: 'PopUpInput',
							style: {
								border: 'none'
							}
						}, 'Titre'))),
				app.createElement('div', {
					style: {
						display: 'flex',
						flexDirection: 'row',
						gap: '20px'
					}

				},
					app.createElement('div', {
						style: {
							display: 'flex',
							flexDirection: 'column'
						}
					},
						app.createElement('label', {}, "Prénom"),

						app.createElement('div', {
							style: {
								display: 'flex',
								border: '3px solid #85D3FF',
								borderRadius: '10px',
								overflow: 'hidden',
								height: '40px'
							}
						},
							app.createElement('img', {
								src: '//static.projet-web.fr/public/img/users.svg'
							}, 'Titre'),

							app.createElement('input', {
								Placeholder: "Prénom",
								class: 'PopUpInput',
								style: {
									border: 'none',
								}
							}, 'Titre'))),
					app.createElement('div', {
						style: {
							display: 'flex',
							flexDirection: 'column'
						}
					},
						app.createElement('label', {}, "Nom"),
						app.createElement('div', {
							style: {
								display: 'flex',
								border: '3px solid #85D3FF',
								borderRadius: '10px',
								overflow: 'hidden',
								height: '40px'
							}
						},
							app.createElement('img', {
								src: '//static.projet-web.fr/public/img/users.svg'
							}, 'Titre'),
							app.createElement('input', {
								Placeholder: "Nom",
								class: 'PopUpInput',
								style: {
									border: 'none',
								}
							}, 'Titre')))),

				app.createElement('div', {
					style: {
						display: 'flex',
						flexDirection: 'row',
						gap: '20px'
					}

				},
					app.createElement('div', {
						style: {
							display: 'flex',
							flexDirection: 'column'
						}
					},
						app.createElement('label', {}, "Etablissement"),
						app.createElement('div', {
							style: {
								display: 'flex',
								border: '3px solid #85D3FF',
								borderRadius: '10px',
								overflow: 'hidden',
								height: '40px'
							}
						},
							app.createElement('img', {
								src: '//static.projet-web.fr/public/img/users.svg'
							}, 'Titre'),

							app.createElement('input', {
								Placeholder: "Etablissement",
								class: 'PopUpInput',
								style: {
									border: 'none',
								}
							}, 'Titre'))),
					app.createElement('div', {
						style: {
							display: 'flex',
							flexDirection: 'column'
						}
					},
						app.createElement('label', {}, "Promotion"),
						app.createElement('div', {
							style: {
								display: 'flex',
								border: '3px solid #85D3FF',
								borderRadius: '10px',
								overflow: 'hidden',
								height: '40px'
							}
						},
							app.createElement('img', {
								src: '//static.projet-web.fr/public/img/users.svg'
							}, 'Titre'),
							app.createElement('input', {
								Placeholder: "Promotion",
								class: 'PopUpInput',
								style: {
									border: 'none',
								}
							}, 'Titre')))),
			),
		],
		buttons: [
			{
				text: 'Supprimer le compte', color: '#FF6262', filled: true, action: (close) => {
					app.dialog.confirm({
						title: "Supprimer un utilisateur",
						content: [
							'Voulez vous vraiment supprimer cet utilisateur ?'
						]
					}).then(() => close()).catch(() => { })
				}
			},

			{ text: 'Annuler', action: 'close' },
			{ text: 'Valider',color : 'var(--color-blue-600)',filled: true, action: (close) => { close(); } }
		]
	}).show();
});