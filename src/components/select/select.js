document.querySelectorAll(".c-select").forEach((select) => {
	select.addEventListener("click", (e) => {
		if (e.target.closest(".c-select_options")) return;
		e.preventDefault();

		let currentState = select.getAttribute("data-opened");
		let callback = function (event) {
			if (
				event.target.closest(".c-select_options") !==
				select.querySelector(".c-select_options")
			) {
				select.removeAttribute("data-opened");
				document.body.removeEventListener("click", callback);
			}
		};

		if (currentState === "true") {
			select.removeAttribute("data-opened");
			document.body.removeEventListener("click", callback);
		} else {
			select.setAttribute("data-opened", null);
			document.body.addEventListener("click", callback, true);
		}
	});

	let selectedOptions = [];
	for (let option of select.querySelectorAll(".c-select_option")) {
		if (option.hasAttribute("data-selected")) {
			selectedOptions.push(option.getAttribute("data-value"));
		}

		option.addEventListener("click", (e) => {
			e.preventDefault();
			if (option.hasAttribute("data-selected")) {
				if (!select.hasAttribute("data-multiple")) select.removeAttribute("data-selected");
				else
					select.dataset.selected = select.dataset.selected
						.split(",")
						.filter((value) => value !== option.getAttribute("data-value"))
						.join(",");
				option.removeAttribute("data-selected");
			} else {
				if (!select.hasAttribute("data-multiple")) {
					select.querySelectorAll(".c-select_option").forEach((opt) => {
						opt.removeAttribute("data-selected");
					});
					select.dataset.selected = option.getAttribute("data-value");
				} else
					select.dataset.selected = select.dataset.selected
						.split(",")
						.concat(option.getAttribute("data-value"));
				option.setAttribute("data-selected", null);
			}
		});
	}

	// select.setAttribute("data-selected", selectedOptions.join(","));
	select.dataset.selected = selectedOptions;
});
