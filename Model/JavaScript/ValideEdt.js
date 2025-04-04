function confirmerAction() {
    var confirmation = confirm("Êtes-vous sûr de vouloir valider la version actuelle ?");
    if (confirmation) {
        document.querySelector("input[name='action']").value = "valider";//Prend le input avec le nom action et lui attribut "valider" @Bastien
        var message = document.getElementById('validationMessage')
        message.innerText = "La validation a été prise en compte.";
        message.style.display = "block";
        document.getElementById("validation").submit();
    }
}

function annulerValidation() {
    var confirmation = confirm("Êtes-vous sûr de vouloir annuler la validation de la version actuelle ?");
    if (confirmation) {
        document.querySelector("input[name='action']").value = "annuler";
        document.getElementById("validation").submit();
    }
}

function vider(){
    var confirmation = confirm("Êtes-vous sûr de vouloir vider les validations actuelle ?");
    if(confirmation) {
        document.querySelector("input[name='action']").value = "vider";
        var message = document.getElementById('validationMessage')
        message.innerText = "Les validations ont été vidées avec succès.";
        message.style.display = "block";
        let AnnulerValidation = document.getElementById("validation");
        AnnulerValidation.addEventListener('click', function (e) {
            e.preventDefault()
            chargerTableau()
        })
    }
}

function validationAdmin(){
    var confirmation = confirm("Êtes-vous sûr(e) de vouloir valider la version de l'emploi du temps ? Cette action est irréversible.");
    if(confirmation) {
        document.querySelector("input[name='actionAdminValide']").value = "adminValider";
        var message = document.getElementById('validationMessage')
        message.innerText = "La version de l'emploi du temps a été validée avec succès.";
        document.getElementById('ValiderVersionAdmin').style.display = 'none';
        document.getElementById("adminValide").submit();
    }
}

function cacher(){
    const cookies = document.cookie;
    if (!cookies.includes('administrateur')) {
        document.getElementById('Vider').style.display = 'none';
        document.getElementById('ValiderVersionAdmin').style.display = 'none';
    }
    else {
        document.getElementById('ValiderVersion').style.display = 'none';
        document.getElementById('AnnulerValidation').style.display = 'none';
    }
}

function choisirVersion(){
    let menuVersion = document.getElementById("menu");
    let valeur = menuVersion.value;
    let expirationDate = new Date(new Date().getTime() + 15 * 60 * 1000).toUTCString();
    document.cookie = "version=" + valeur + "; expires=" + expirationDate + "; path=/";
    location.reload()
}

function chargerEdt(selectedDate) {

    const data = new URLSearchParams();
    data.append('selectedDate', selectedDate);

    fetch('../../View/Pages/ValideEdt.php', {
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

function chargerTableau() {
    console.log("Chargement du tableau...");  // Vérifier que cette ligne s'affiche dans la console
    const data = new URLSearchParams();

    fetch('../../View/Pages/ValideEdt.php', {
        method: 'POST',
        body: data,
    })
        .then(response => response.text())
        .then(responseText => {
            console.log("Réponse reçue:", responseText);  // Vérifier la réponse du serveur
            // Utiliser DOMParser pour trier la réponse HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(responseText, 'text/html');

            // Prendre seulement la div tableau de la réponse
            const tableau = doc.querySelector('#tableau');

            // Mettre à jour uniquement la div tableau dans la page actuelle
            if (tableau) {
                document.getElementById('tableau').innerHTML = tableau.innerHTML;
            }
        })
        .catch(error => console.error('Erreur:', error));
}



//Ajax :
document.addEventListener('DOMContentLoaded', function () {
    const boutonPrecedent = document.getElementById('precedent')
    const boutonSuivant = document.getElementById('suivant')
    const selectionnerSemaine = document.getElementById('selectionnerSemaine')
    const ValiderVersion = document.getElementById('ValiderVersion')
    const AnnulerValidation = document.getElementById('AnnulerValidation')
    const Vider = document.getElementById('Vider')

    Vider.addEventListener('click', function (e) {
        e.preventDefault()
        chargerTableau()
    })

    // Change la valeur de l'EDT en fonction du bouton calendrier @Noah
    selectionnerSemaine.addEventListener('change', function (e) {
        e.preventDefault()
        chargerEdt(ValiderVersion.value)
    })

    // Décrémente l'EDT lors d'un click sur la flèche précédent @Noah
    boutonPrecedent.addEventListener('click', function (e) {
        e.preventDefault()
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() - 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdt(selectionnerSemaine.value);

    })

    // Incrémente l'EDT lors d'un click sur la flèche précédent @Noah
    boutonSuivant.addEventListener('click', function (e) {
        e.preventDefault();
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() + 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdt(selectionnerSemaine.value);
    });
});

cacher()