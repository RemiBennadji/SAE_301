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

// const idcompte = document.getElementById('idcompte');
// const idpsw = document.getElementById('idpsw');
// const labelIdentifiant = document.getElementById('labelIdentifiant')
// const labelPsw = document.getElementById('labelPsw')
// const yeux = document.getElementById('passwordSymbole')
//
// document.getElementById('formID').addEventListener('submit', function (event){
//     event.preventDefault()
//     idcompte.style.background = '#f2a19b'
//     idcompte.borderWidth = '50px'
//     idcompte.style.border = 'RED'
//     labelIdentifiant.style.color = 'RED'
//     idpsw.style.background = '#f2a19b'
//     idpsw.style.border = 'RED'
//     labelPsw.style.color = 'RED'
//     yeux.style.color = 'BLACK'
// });

/*
document.getElementById("submitID").addEventListener('click', function(){
    const IdCompte = document.getElementById('idcompte');
    const MDP = document.getElementById('idpsw');
    const formInscription = document.getElementById('formID');

    function AlerteBox(event){
        event.preventDefault();
        let errorMessage = '';
        if (errorMessage.value.trim()!==''){
            alert(errorMessage)
        } else {
            header("location:../Controller/identification.php")
        }
    }
});
 */