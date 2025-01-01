function confirmerAction() {
    var confirmation = confirm("Êtes-vous sûr de vouloir valider la version actuelle ?");
    if (confirmation) {
        document.querySelector("input[name='action']").value = "valider";
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
