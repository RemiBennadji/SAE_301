function admin() {
    document.getElementById("creationCompte").style.display = "block";
    document.getElementById("edtCours").style.display = "block";
    document.getElementById("choixClasse").style.display = "block";
}

function etudiant() {
}

function professeur() {
    document.getElementById("edtCours").style.display = "block";
    document.getElementById("edtProf").style.display = "block";
    document.getElementById("demande").style.display = "block";
    document.getElementById("edt").style.display = "none";
}

function secretariat() {
    document.getElementById("afficheSalles").style.display = "block";
    document.getElementById("edtCours").style.display = "block";
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

//sert à afficher le bon emploi du temps choisit par l'administrateur
document.addEventListener('DOMContentLoaded', function () {
    const edtAdmin = document.getElementById('edtAdmin');
    if (edtAdmin) {
        edtAdmin.addEventListener('change', function () {
            // Vérification de la valeur de 'edtAdmin' avant de l'utiliser
            console.log("Valeur sélectionnée pour 'edtAdmin':", edtAdmin.value);

            // Fonction pour calculer l'année en fonction du groupe
            function anneEtu(groupe){
                let annee = 0;
                // Année 1
                if ((groupe === 'A1') || (groupe === 'A2') || (groupe === 'B1') || (groupe === 'B2') || (groupe === 'C1') || (groupe === 'C2')){
                    annee = 1;
                }
                // Année 2
                else if ((groupe === 'FIA1') || (groupe === 'FIA2') || (groupe === '2FIB') || (groupe === '2FA')) {
                    annee = 2;
                }
                // Année 3
                else if ((groupe === 'FIA') || (groupe === 'FIB') || (groupe === 'FA')) {
                    annee = 3;
                }
                // MPH
                else if ((groupe === 'MPH')) {
                    annee = 0;
                }
                return annee;
            }

            // Cookie qui expire dans 15 min en enregistrant le groupe et l'année
            let expirationDate = new Date(new Date().getTime() + 15 * 60 * 1000).toUTCString();
            console.log("Expiration Cookie:", expirationDate);

            // Enregistrement des cookies
            document.cookie = "annee=" + anneEtu(edtAdmin.value) + "; expires=" + expirationDate + "; path=/";

            // Pour éviter les doublons entre 2ème et 3ème année
            if (edtAdmin.value[0]==='2'){
                document.cookie = "groupe=" + edtAdmin.value.slice(1) + "; expires=" + expirationDate + "; path=/";
                }
            else {
                document.cookie = "groupe=" + edtAdmin.value + "; expires=" + expirationDate + "; path=/";
            }
            window.location.reload();
        });
    } else {
        console.error("L'élément edtAdmin n'a pas été trouvé.");
    }
});
