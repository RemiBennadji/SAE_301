function verifyMDP() {
    var mdp = document.getElementById("mdp").value;
    var mdpVerif = document.getElementById("mdpverify").value;

    // Vérifier si les mots de passe correspondent
    if (mdp === mdpVerif) {

    } else {
        var erreur = document.getElementById("erreur");
        erreur.style.display = "block";
    }
}

// Ajouter un écouteur d'événements sur le formulaire pour appeler verifyMDP lors de la soumission
document.getElementById("changeMDPForm").addEventListener("submit", function(event) {
    event.preventDefault();
    verifyMDP();
});
