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
        document.getElementById("validation").submit();
    }
}

function cacher(){
    const cookies = document.cookie;
    if (!cookies.includes('administrateur')) {
        document.getElementById('Vider').style.display = 'none';
    }
    else {
        document.getElementById('ValiderVersion').style.display = 'none';
        document.getElementById('AnnulerValidation').style.display = 'none';
    }
}

cacher()