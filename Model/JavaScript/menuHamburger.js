<!-- Script pour faire fonctionner le menu burger (affichage mobile) -->
const burger = document.querySelector('.burger');
const menu = document.querySelector('.menu');
burger.addEventListener("click", () => {
    menu.classList.toggle("active");
    burger.classList.toggle("toggle");
});