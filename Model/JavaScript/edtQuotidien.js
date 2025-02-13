document.querySelectorAll('.input2').forEach(input => {
    input.addEventListener('change', function () {
        // Vérifier quelle option est sélectionnée
        if (this.value === 'option1') {
            // Ouvrir une nouvelle fenêtre ou un nouvel onglet pour l'option "Quotidien"
            window.open('https://example.com/quotidien', '_blank');
        } else if (this.value === 'option2') {
            // Ouvrir une nouvelle fenêtre ou un nouvel onglet pour l'option "Hebdomadaire"
            window.open('https://example.com/hebdomadaire', '_blank');
        }
    });
});