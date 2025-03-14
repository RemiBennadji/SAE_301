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

//sert à afficher le bon emploi du temps choisit par l'administrateur
document.addEventListener('DOMContentLoaded', function () {
    const boutonPrecedent = document.getElementById('precedent')
    const boutonSuivant = document.getElementById('suivant')
    const selectionnerSemaine = document.getElementById('selectionnerSemaine')

    function chargerEdt(selectedDate) {

        const data = new URLSearchParams();
        data.append('selectedDate', selectedDate);

        fetch('../../View/Pages/EDT.php', {
            method: 'POST',
            body: data,
        })
            .then(response => response.text())
            .then(responseText => {
                // Utiliser DOMParser pour trier la réponse HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(responseText, 'text/html');

                // Prendre seulement la div edtContainer de la réponse
                const newEdt = doc.querySelector('#edtContainer');

                // Mettre à jour uniquement la div edtContainer dans la page actuelle
                document.getElementById('edtContainer').innerHTML = newEdt.innerHTML;
            })
            .catch(error => console.error('Erreur:', error));
    }

    boutonPrecedent.addEventListener('click', function(e){
        e.preventDefault()
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() - 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdt(selectionnerSemaine.value);

    })

    boutonSuivant.addEventListener('click', function (e) {
        e.preventDefault();
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() + 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdt(selectionnerSemaine.value);
    });



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
        });
    } else {
        console.error("L'élément edtAdmin n'a pas été trouvé.");
    }
});
