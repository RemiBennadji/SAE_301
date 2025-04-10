document.addEventListener('DOMContentLoaded', function () {
    const selectionnerSemaine = document.getElementById('selectionnerSemaine')
    const boutonJourPrecedent = document.getElementById('jourPrecedent')
    const boutonJourSuivant = document.getElementById('jourSuivant')

    // Permet de recharger l'edtQuotidient sans recharger toute la page @mattheo
    function chargerEdtJour(selectedDate) {

        const data = new URLSearchParams();
        data.append('selectedDate', selectedDate);

        fetch('../../View/Pages/edtQuotidien.php', {
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

    // Décrémente l'EDT lors d'un click sur la flèche Jourprécédent @Mattheo
    boutonJourPrecedent.addEventListener('click', function(e){
        e.preventDefault()
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() - 1);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdtJour(selectionnerSemaine.value);
    })

    // Incrémente l'EDT lors d'un click sur la flèche Joursuivant @Mattheo
    boutonJourSuivant.addEventListener('click', function (e) {
        e.preventDefault();
        const currentDate = new Date(selectionnerSemaine.value);
        currentDate.setDate(currentDate.getDate() + 1);
        selectionnerSemaine.value = currentDate.toISOString().split('T')[0];
        chargerEdtJour(selectionnerSemaine.value);
    });
}