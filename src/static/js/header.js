app.onload(() => {
	const $account = document.querySelector('header .account');
	const $dropdown = document.querySelector('header .account .dropdown');
	const $hamburger = document.querySelector('header .hamburger');
	const $navBar = document.querySelector('header nav');
	const $backdrop = document.querySelector('header .backdrop');
    const $close = document.querySelector('header .close');

	const isDropdownShown = new Proxy(
		{
			value: $dropdown.classList.contains('visible')
		},
		{
			set(target, key, value) {
				if (key === 'value' && typeof value === 'boolean') {
					target[key] = value;
					$dropdown.classList.toggle('visible', value);
					return true;
				}
			}
		}
	);

	const isMenuShown = new Proxy(
		{
			value: $navBar.classList.contains('visible')
		},
		{
			set(target, key, value) {
				if (key === 'value' && typeof value === 'boolean') {
					target[key] = value;
					$navBar.classList.toggle('visible', value);
					$backdrop.classList.toggle('visible', value);
					return true;
				}
			}
		}
	);

	$account.addEventListener('click', () => {
		isDropdownShown.value = !isDropdownShown.value;
	});
	$hamburger.addEventListener('click', () => {
		isMenuShown.value = !isMenuShown.value;
	});
	$backdrop.addEventListener('click', () => {
		isMenuShown.value = false;
	});
    $close.addEventListener('click', () => {
        isMenuShown.value = false;
    });
});
