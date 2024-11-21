document.getElementById('togglePassword').addEventListener('click', function () {
    var password = document.getElementById("idpsw");

    // Bascule entre les types "password" et "text"
    if (password.type === "password") {
        password.type = "text";
        // Change l'icône en œil barré
        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        password.type = "password";
        // Remet l'icône en œil ouvert
        this.innerHTML = '<i class="fas fa-eye"></i>';
    }
});

const idcompte = document.getElementById('idcompte');
const idpsw = document.getElementById('idpsw');
const labelIdentifiant = document.getElementById('labelIdentifiant')
const labelPsw = document.getElementById('labelPsw')
const yeux = document.getElementById('passwordSymbole')

document.getElementById('formID').addEventListener('submit', function (event) {
    console.log('Gestionnaire de soumission appelé');
    event.preventDefault(); // Empêche l'envoi normal du formulaire

    const formData = new FormData(this);
    console.log('Données du formulaire :', [...formData.entries()]);

    fetch('../../Controller/Identification.php', {method: 'POST'/*POST = cacher info URL*/, body: formData})//envoie donnée au serveur et return réponse
        .then(response => response.text())// enregistre la reponse du serveur
        .then(data => {//si il y a une reponse
            console.log('Réponse du serveur :', data);
            if (data === 'fail') {
                // Change le style en cas d'échec
                idcompte.style.background = '#f2a19b';
                idcompte.style.border = '2px solid red';
                labelIdentifiant.style.color = 'red';

                idpsw.style.background = '#f2a19b';
                idpsw.style.border = '2px solid red';
                labelPsw.style.color = 'red';
                yeux.style.color = 'black';
            } else {
                // Redirige l'utilisateur s'il est authentifié
                window.location.href = '../../Controller/MenuPrincipal.php';
            }
        })
        .catch(error => console.error('Erreur:', error));
});

