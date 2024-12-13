document.addEventListener('DOMContentLoaded', function() {
    const selectionnerSemaine = document.getElementById('selectionnerSemaine');
    const dateActuel = document.querySelector('input[name="dateActuel"]');

    selectionnerSemaine.addEventListener('change', function(e) {
        // Obtenir la date sélectionnée
        let selectedDate = new Date(this.value);

        // Obtenir le jour de la semaine (0 = dimanche, 1 = lundi, etc.)
        let dayOfWeek = selectedDate.getDay();

        // Ajuster au lundi de la semaine, pour éviter le décalage des cours
        if (dayOfWeek === 0) {
            selectedDate.setDate(selectedDate.getDate() - 6);
        } else {
            selectedDate.setDate(selectedDate.getDate() - (dayOfWeek - 1));
        }

        // Formater la date pour le format Y-m-d
        let mondayDate = selectedDate.toISOString().split('T')[0];

        // Mettre à jour la valeur de l'input hidden
        dateActuel.value = mondayDate;

        // Mettre à jour la valeur du sélecteur de semaine
        this.value = mondayDate;
    });
});