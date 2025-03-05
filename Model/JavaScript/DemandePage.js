window.onload = function() {
    const reportDate = sessionStorage.getItem('reportDate');
    const reportHeure = sessionStorage.getItem('reportHeure');

    if (reportDate) {
        document.getElementById('dateReport').value = reportDate;
    }
    if (reportHeure) {
        document.getElementById('heureStartReport').value = reportHeure.slice(0,5);
        document.getElementById('heureEndReport').value = "17:00";
    }

}