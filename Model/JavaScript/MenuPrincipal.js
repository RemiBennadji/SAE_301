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

function voireEDTGlobal() {
    //Recuperes tous les cookie séparé par un ;
    let cookies = document.cookie.split(';');
    //Pour tous les cookies il les separent avec des = pour preparer la liste avec des caracteres visibles (evite le %20 pour les espaces par exemple)
    for (let cookie of cookies) {
        let [clef, valeur] = cookie.trim().split('=');//trim() : evite les espaces, //split() : divise le cookie en 2 pour faire une liste avec ca clef et ca valeur
        if ((clef === "administrateur") || (clef === "secretariat")) {
            return true;
        }
    }
    return false;
}

function anneEtu(groupe){
    annee = 0;
    //Annee 1
    if ((groupe === 'A1') || (groupe === 'A2') || (groupe === 'B1') || (groupe === 'B2') || (groupe === 'C1') || (groupe === 'C2')){annee = 1;}
    //Annee 2
    else if ((groupe === 'FIA1') || (groupe === 'FIA2') || (groupe === '2FIB') || (groupe === '2FA')) { annee = 2;}
    //Annee 3
    else if ((groupe === 'FIA') || (groupe === 'FIB') || (groupe === 'FA')) { annee = 3;}
    //MPH
    else if ((groupe === 'MPH')) { annee = 0;}

    return annee;
}

function edtAdmin() {
    const edtAdmin = document.getElementById('edtAdmin');
    const edt = document.getElementById('edt');
    //Affichages des elements selons le role
    if(voireEDTGlobal()) {
        document.getElementById("edtAdmin").style.display = "none";
    }
    else{
        document.getElementById("edt").style.display = "none";
    }

    edtAdmin.addEventListener('change', function () {
        //Cookie qui expire dans 15 min en enregistrant le groupe et l'annee
        document.cookie = "groupe=" + edtAdmin.value + "; expires=" + new Date(new Date().getTime() + 15 * 60 * 1000).toUTCString() + "; path=/";
        document.cookie = "annee=" + anneEtu(edtAdmin.value) + "; expires=" + new Date(new Date().getTime() + 15 * 60 * 1000).toUTCString() + "; path=/";
    });
}

edtAdmin();

