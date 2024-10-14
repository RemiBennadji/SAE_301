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