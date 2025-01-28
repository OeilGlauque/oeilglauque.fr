/** Animation navBar au défilement sur la page d'accueil **/

const navbar = document.getElementById('header');

window.addEventListener('scroll', function () {
    if (window.scrollY > 0) {
        navbar.classList.add('scrolled'); // Ajoute la classe lorsque la page est scrollée
    } else {
        navbar.classList.remove('scrolled'); // Retire la classe si on est en haut de la page
    }
});
