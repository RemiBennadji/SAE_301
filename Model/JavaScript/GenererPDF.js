function generation() {
    var date = document.getElementById("dateDuJour").textContent

    document.getElementById('download-pdf').addEventListener('click', function () {
    const {jsPDF} = window.jspdf;
    const doc = new jsPDF('landscape');

    // Titre
    doc.setFontSize(15);
    doc.text('Emploi du Temps - ' + date, 14, 20);

    // Configuration de la table avec autoTable
    doc.autoTable({
        html: 'table', // Prend le tableau
        startY: 30, // Position verticale du début du tableau
        theme: 'grid', // Theme grille
        headStyles: {//En-tête
            fillColor: [0, 129, 161], // Fond de la premiere ligne
            textColor: [255, 255, 255],
            fontSize: 8,
            fontStyle: 'bold', // Gras
    },
    bodyStyles: {
        fontSize: 10, // Taille de la police pour le corps du tableau
    },
    columnStyles: {
        0: {cellWidth: 12}, // Largeur de la première colonne
    },
    margin: {top: 20, left: 10, bottom: 10}, // Marges du tableau dans le document
});

    // Télécharger le PDF généré
    doc.save('emploi_du_temps.pdf');
});
}

generation()