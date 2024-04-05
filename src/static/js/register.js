/// <reference path="base.js" />

app.onload(function () {
	const $form = document.querySelector('form');

	/*
    const regex = { //même contraintes que pour le login
		username: /^[a-zA-Z0-9]{3,30}$/,
		password: /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/
	};

    if (!regex.username.test(username)) { //vérification du nom d'utilisateur comme pour login
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

    if (!regex.password.test(password)) { //vérification du mot de passe comme pour login
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

    */

	// Envoyer les données au serveur en utilisant fetch
	$form.addEventListener('submit', function (event) {
		event.preventDefault();

		var formData = {
			username: document.getElementById('username').value,
			password: document.getElementById('password').value,
			firstname: document.getElementById('firstname').value,
			lastname: document.getElementById('name').value,
			school: document.getElementById('school').value,
			class: document.getElementById('class').value
		};

		fetch('/api/register', {
			method: 'POST',
			body: JSON.stringify(formData) // convertisser l'objet formData en chaîne JSON
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status == 200) {
					app.toast.success('Inscription réussie. Redirection dans 3 secondes...');
					setTimeout(() => {
						window.location.href = '/login';
					}, 3000);
				} else {
					app.toast.error("Erreur lors de l'inscription.");
				}
			})
			.catch((error) => {
				app.toast.error("Erreur lors de l'inscription.");
			});
	});
});
