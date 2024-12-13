

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
        .then(response =>{ if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }
            return response.text();})// enregistre la reponse du serveur
        .then(data => {//si il y a une reponse
            console.log("Réponse brute :", data);
            try{
                const jsonData = JSON.parse(data);
                if (jsonData.error){
                    console.error(jsonData.error);

                    if (jsonData.error === 'errorConnexion') {
                        // Change le style en cas d'échec
                        idcompte.style.background = '#f2a19b';
                        idcompte.style.border = '2px solid red';
                        labelIdentifiant.style.color = 'red';

                        idpsw.style.background = '#f2a19b';
                        idpsw.style.border = '2px solid red';
                        labelPsw.style.color = 'red';
                        yeux.style.color = 'black';
                    }
                }else if (jsonData.redirect) {
                    // Effectue la redirection vers l'URL fournie par le PHP
                    window.location.href = jsonData.redirect;
                }

            }catch (error){
                console.error("Erreur lors du parsing JSON : ", error);
            }
        })

        .catch(error => {
            console.error('Erreur:', error.message);
        });
})

