window.onload = function() {
    const reportDate = sessionStorage.getItem('reportDate');
    const reportHeure = sessionStorage.getItem('reportHeure');

    if (reportDate) {
        document.getElementById('dateReport').value = reportDate;
    }
    if (reportHeure) {
        document.getElementById('heureReport').value = reportHeure;
    }
}