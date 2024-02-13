document.querySelectorAll(".c-textarea").forEach((cTextarea) => {
    /* Cible le champ de texte lorsque le composant est cliqué */
	cTextarea.addEventListener("click", (event) => {
		if (event.target.tagName === "TEXTAREA" || event.target.tagName === "INPUT") return;
		cTextarea.querySelector("textarea")?.focus() || cTextarea.querySelector("input")?.focus();
	});
});