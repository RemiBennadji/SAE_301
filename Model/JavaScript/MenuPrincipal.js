function admin() {
    document.getElementById("creationCompte").style.display = "block";
    document.getElementById("afficheSalles").style.display = "block";
}

function etudiant() {
}

function professeur() {
}

function secretariat() {
    document.getElementById("afficheSalles").style.display = "block";
}

function afficherElement(role) {//Fonction qui verifie le rôle de l'utilisateur
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

function edtAdmin() {
    const element = document.getElementById('edtAdmin');
    element.addEventListener('change', function () {
        //Cookie qui expire dans 15 min en enregistrant le groupe
        document.cookie = "groupe=" + element.value + "; expires=" + new Date(new Date().getTime() + 15 * 60 * 1000).toUTCString() + "; path=/";
    });
}

edtAdmin();

