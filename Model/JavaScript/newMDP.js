document.getElementById('formID').addEventListener('submit', function (event) {
    event.preventDefault(); // Empêche l'envoi normal du formulaire

    const formData = new FormData(this);

    fetch('../../Controller/changeMDP.php', {method: 'POST'/*POST = cacher info URL*/, body: formData})//envoie donnée au serveur et return réponse
        .then(response => response.text())// enregistre la reponse du serveur
        .then(data => {//si il y a une reponse
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