function admin(){
    document.getElementById("creationCompte").style.display="block";
}

function etudiant(){}

function professeur(){}

function secretariat(){}

function afficherElement(role){//Fonction qui verifie le rôle de l'utilisateur
    if(role === "administrateur"){
        admin();
    }
    else if(role === "secretariat"){
        secretariat();
    }
    else if(role === "etudiant"){
        etudiant();
    }
    else if(role === "professeur"){
        professeur();
    }
    console.log(role);
}

