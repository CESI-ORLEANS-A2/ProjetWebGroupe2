app.onload(function() {
    const $seconds = document.querySelector('.seconds');
    
    let timer = 5;

    setInterval(() => {
        $seconds.textContent = timer;
        timer--;

        if (timer < 0) {
            window.location.href = '/';
        }
    }, 1000);
})