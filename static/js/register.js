/// <reference path="base.js" />


app.onload(function () {
    var formData = {
        username: document.getElementById('username'),
        password: document.getElementById('password'),
        firstname: document.getElementById('firstname'),
        lastname: document.getElementById('name'),
        establishment: document.getElementById('school'),
        promotion: document.getElementById('class'),
    };

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

    

        // Envoyer les données au serveur en utilisant fetch
        fetch('/api/Register.php', {
            method: 'POST', 
            body: JSON.stringify(formData), // convertisser l'objet formData en chaîne JSON
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
        })
        .catch((error) => {
            app.toast.error("Erreur lors de l'inscription.");
        });
    });

