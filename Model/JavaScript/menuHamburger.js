<!-- Script pour faire fonctionner le menu burger (affichage mobile) -->
const burger = document.querySelector('.burger');
const menu = document.querySelector('.menu');
burger.addEventListener("click", () => {
    menu.classList.toggle("active");
    burger.classList.toggle("toggle");
});

function admin() {
    document.getElementById("edtCours").style.display = "block";
    document.getElementById("afficheSalles").style.display = "block";
    document.getElementById("tableauEtudiant").style.display = "block";
    document.getElementById("tableauAbsence").style.display = "block";
    document.getElementById("creationCompte").style.display = "block";
    document.getElementById("tableauAbsence").style.display = "block";
    document.getElementById("tableauReport").style.display = "block";
    document.getElementById("valideEDT").style.display = "block";
    document.getElementById("choixClasse").style.display = "block";
}

function etudiant() {
}

function professeur() {
    document.getElementById("edtCours").style.display = "block";
    document.getElementById("edtProf").style.display = "block";
    document.getElementById("afficheSalles").style.display = "block";
    document.getElementById("valideEDT").style.display = "block";
    document.getElementById("demande").style.display = "block";
    if(document.getElementById("edt")){
        document.getElementById("edt").style.display = "none";
    }
    let element = document.getElementById('menu');
    if(element){
        element.classList.remove('menu');
        element.classList.add('menuProf');
    }
}

function secretariat() {
    document.getElementById("edtCours").style.display = "block";
    document.getElementById("afficheSalles").style.display = "block";
    document.getElementById("tableauEtudiant").style.display = "block";
    document.getElementById("tableauAbsence").style.display = "block";
    document.getElementById("choixClasse").style.display = "block";
}

//Fonction qui verifie le rôle de l'utilisateur car suivant le rôle, nous affichons ou cachons des informations
function afficherElement(role) {
    if (role === "administrateur") {
        admin();
    } else if (role === "secretariat") {
        secretariat();
    } else if (role === "etudiant") {
        etudiant();
    } else if (role === "professeur") {
        professeur();
    }
    console.log(role);
}