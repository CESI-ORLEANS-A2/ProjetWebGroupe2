app.onload(function () {
	const $wishList = document.querySelector('.wish-list');
	const $apply = document.querySelector('.apply');
	const $applyText = document.querySelector('.apply .c-button span');

	$wishList?.addEventListener('click', function (e) {
		e.preventDefault();

		const offer = new URLSearchParams(window.location.search).get('id');

		if (!offer || isNaN(offer)) {
			app.toast.error('Une erreur est survenue');
			return;
		}

		fetch('/api/wishlist?offer=' + offer, {
			method: 'POST'
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (data) {
				if (data.status === 200) {
					if (data.body?.isInWishList) {
						$wishList.classList.add('active');
						app.toast.success('Offre ajoutée à votre liste de souhaits');
					} else {
						$wishList.classList.remove('active');
						app.toast.info('Offre retirée de votre liste de souhaits');
					}
				} else {
					app.toast.error('Une erreur est survenue');
				}
			})
			.catch(function (error) {
				app.toast.error('Une erreur est survenue');
			});
	});

	$apply?.addEventListener('click', function (e) {
		e.preventDefault();

		const offer = new URLSearchParams(window.location.search).get('id');

		if (!offer || isNaN(offer)) {
			app.toast.error('Une erreur est survenue');
			return;
		}

		app.dialog.confirm({
			content: 'Êtes-vous sûr de vouloir continuer ?',
			onConfirm() {
				fetch('/api/apply?offer=' + offer, {
					method: 'POST'
				})
					.then(function (response) {
						return response.json();
					})
					.then(function (data) {
						if (data.status === 200) {
							if (data.body?.isApplied) {
								app.toast.success('Votre candidature a bien été envoyée');
								$applyText.textContent = 'Retirer ma candidature';
							} else {
								app.toast.info('Votre candidature a bien été retirée');
								$applyText.textContent = 'Postuler';
							}
						} else {
							app.toast.error('Une erreur est survenue');
						}
					})
					.catch(function (error) {
                        console.error(error);
						app.toast.error('Une erreur est survenue');
					});
			}
		}).show();
	});
});
