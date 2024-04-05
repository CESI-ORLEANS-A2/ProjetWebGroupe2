app.onload(function () {
    const $input = document.querySelector('.search .c-textarea');
    const $sumbit = document.querySelector('.search .c-button');

    $sumbit?.addEventListener('click', function () {
        if ($input.__component.data.value) {
            location.href = `/search?query=${$input.__component.data.value}&type=offer`;
        }
    });
})