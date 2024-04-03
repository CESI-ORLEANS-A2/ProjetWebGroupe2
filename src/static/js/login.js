app.onload(function () {
	const $username = document.getElementById('username');
	const $usernameTextarea = $username.querySelector('.c-textarea_box');
	const $password = document.getElementById('password');
	const $passwordTextarea = $password.querySelector('.c-textarea_box');
	const $form = document.querySelector('main form');

	const regex = {
		username: /^[a-zA-Z0-9]{3,30}$/,
		password: /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/
	};

	$form.addEventListener('submit', function (event) {
		event.preventDefault();

		const username = $username.__component.data.value;
		const password = $password.__component.data.value;

		if (!regex.username.test(username)) {
			if ($usernameTextarea.__tooltip) return;

			const tooltip = app.tooltip.show(
				$usernameTextarea,
				"Le nom d'utilisateur doit contenir entre 3 et 30 caractères alphanumériques",
				{
					delay: 0
				}
			);

			$username.addEventListener('input', () => tooltip.remove(), { once: true });
			return;
		}

		if (!regex.password.test(password)) {
			if ($passwordTextarea.__tooltip) return;
			const tooltip = app.tooltip.show(
				$passwordTextarea,
				'Le mot de passe doit contenir entre 8 et 16 caractères, au moins une minuscule, une majuscule, un chiffre et un caractère spécial',
				{
					delay: 0
				}
			);

			$password.addEventListener('input', () => tooltip.remove(), { once: true });
			return;
		}

		fetch('/api/login', {
			method: 'POST',
			body: JSON.stringify({ username, password })
		})
			.then((response) => response.json())
			.then(function (response) {
				if (response.status === 200) {
					document.cookie = `token=${response.body.token}; path=/; samesite=strict`;
					window.location.href = '/';
				} else if (response.status === 401 || response.status === 400) {
					app.toast.error("Nom d'utilisateur ou mot de passe incorrect");
				} else {
					app.toast.error('Une erreur est survenue');
				}
			})
			.catch(function (error) {
				app.toast.error('Une erreur est survenue');
			});
	});
});
