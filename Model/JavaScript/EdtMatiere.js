document.addEventListener('DOMContentLoaded', function () {
    const boutonPrecedent = document.getElementById('precedent');
    const boutonSuivant = document.getElementById('suivant');
    const selectionnerSemaine = document.querySelector('#selectionnerSemaine');

    function chargerEdt(selectedDate) {
        const codeRessource = '<?php echo $_POST["codeRessource"]; ?>'; // Récupérer la valeur PHP initiale
        const dateActuel = document.querySelector('input[name="dateActuel"]').value; // Récupérer la valeur actuelle du champ caché

        const data = new URLSearchParams();
        data.append('selectedDate', selectedDate);
        data.append('codeRessource', codeRessource);
        data.append('dateActuel', dateActuel);
        console.log("selectedDate:", selectedDate);
        console.log("codeRessource:", codeRessource);
        console.log("dateActuel:", dateActuel);

        fetch('../../View/Pages/EDTmatiere.php', {
            method: 'POST',
            body: data,
        })
            .then(response => response.text())
            .then(responseText => {
                console.log("Réponse brute du serveur :", responseText);
                const parser = new DOMParser();
                const doc = parser.parseFromString(responseText, 'text/html');
                const newEdt = doc.querySelector('#edtContainer');
                console.log("Contenu de edtContainer :", newEdt);
                document.getElementById('edtContainer').innerHTML = newEdt ? newEdt.innerHTML : '';
            })
            .catch(error => console.error('Erreur:', error));
    }

    selectionnerSemaine.addEventListener('change', function(){
        chargerEdt(selectionnerSemaine.value);
    });

    boutonPrecedent.addEventListener('click', function(e){
        e.preventDefault();
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() - 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        document.querySelector('input[name="dateActuel"]').value = selectionnerSemaine.value;
        chargerEdt(selectionnerSemaine.value);
    });

    boutonSuivant.addEventListener('click', function (e) {
        e.preventDefault();
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() + 7);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        document.querySelector('input[name="dateActuel"]').value = selectionnerSemaine.value; // Mettre à jour le champ caché
        chargerEdt(selectionnerSemaine.value);
    });

    const edtAdmin = document.getElementById('edtAdmin');
    if (edtAdmin) {
        edtAdmin.addEventListener('change', function () {
            chargerEdt(selectionnerSemaine.value);
        });
    } else {
        console.error("L'élément edtAdmin n'a pas été trouvé.");
    }
});