const mdp = document.getElementById('idpsw');

function verifyMDP() {
    var mdp = document.getElementById("idpsw").value;
    var mdpVerif = document.getElementById("mdpverify").value;

    // Vérifier si les mots de passe correspondent
    if (mdp !== mdpVerif) {
        var erreur = document.getElementById("erreur");
        erreur.style.display = "block";
    }
}
const B2 = document.getElementById("start")
function entrerCode(){
    var B1 = document.getElementById("resend")
    var input = document.getElementById("inputCode")
    var acpt = document.getElementById("accept")
    B1.style.display = "block";
    input.style.display = "block";
    acpt.style.display = "block";
}

B2.addEventListener("click",function (){
   const res = entrerCode();
});

document.getElementById('changeMDPForm').addEventListener('submit', function (event) {
    verifyMDP();
    console.log('Gestionnaire de soumission appelé');
    event.preventDefault(); // Empêche l'envoi normal du formulaire

    const formData = new FormData(this);
    console.log('Données du formulaire :', [...formData.entries()]);

    fetch('../../Controller/changeMDP.php', {method: 'POST'/*POST = cacher info URL*/, body: formData})//envoie donnée au serveur et return réponse
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
                        mdp.style.background = '#f2a19b';
                        mdp.style.border = '2px solid red';
                    }
                }else if (jsonData.redirect) {
                    // Effectue la redirection vers l'URL fournie par le PHP
                    console.log('changementMDP réussi')
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