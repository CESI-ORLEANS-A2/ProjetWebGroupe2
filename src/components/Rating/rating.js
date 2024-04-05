document.addEventListener('DOMContentLoaded', function () {
    var stars = document.querySelectorAll('.star-rating .star');
    stars.forEach(function(star, index){
        star.addEventListener('click', function() { setRating(index + 1); });
        star.addEventListener('mouseover', function() { highlightStars(index); });
        star.addEventListener('mouseout', resetStars);
    });

    function setRating(rating) {
        for (let i = 0; i < stars.length; i++) {
            stars[i].classList.toggle('rated', i < rating);
        }
    }

    function highlightStars(index) {
        for (let i = 0; i < stars.length; i++) {
            stars[i].style.color = i <= index ? '#f5a623' : 'grey';
        }
    }

    function resetStars() {
        stars.forEach(function(star) {
            star.style.color = ''; 
        });
    }
});