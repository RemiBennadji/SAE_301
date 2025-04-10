
//sert à afficher le bon emploi du temps choisit par l'administrateur
document.addEventListener('DOMContentLoaded', function () {
    const boutonPrecedent = document.getElementById('precedent')
    const boutonSuivant = document.getElementById('suivant')
    const selectionnerSemaine = document.getElementById('selectionnerSemaine')

    // Permet de recharger l'EDT sans recharger toute la page @Noah
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

    // Change la valeur de l'EDT en fonction du bouton calendrier @Noah
    selectionnerSemaine.addEventListener('change', function(){
        chargerEdt(selectionnerSemaine.value)
    })

    // Décrémente l'EDT lors d'un click sur la flèche précédent @Noah
    boutonPrecedent.addEventListener('click', function(e){
        e.preventDefault()
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() - 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdt(selectionnerSemaine.value);

    })

    // Incrémente l'EDT lors d'un click sur la flèche suivant @Noah
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
            rechargement();
        });

        function rechargement(){
            chargerEdt(selectionnerSemaine.value);
        }

    } else {
        console.error("L'élément edtAdmin n'a pas été trouvé.");
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.form-import');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(form); // Crée un objet FormData à partir du formulaire
        console.log('test1')
        // Création d'une requête AJAX pour soumettre le fichier CSV
        fetch('../../Controller/creationCompte.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // Attend la réponse en JSON
            .then(data => {
                const feedbackElement = document.getElementById('feedback');
                console.log('test2')
                // Affiche le feedback utilisateur en fonction de la réponse
                if (data.success) {
                    feedbackElement.style.display = 'block';
                    feedbackElement.innerHTML = `Importation réussie ! Nombre de lignes insérées : ${data.count}`;
                    feedbackElement.style.color = 'green';
                } else {
                    feedbackElement.style.display = 'block';
                    feedbackElement.innerHTML = `Erreur : ${data.message}`;
                    feedbackElement.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Erreur AJAX :', error);
            });
    });
});