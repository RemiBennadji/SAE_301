<?php
if(empty($_POST['mdp'])) {
    echo 'Les informations ne sont pas fournies. ';
}

$mdp = $_POST['mdp'];
if($mdp == $_SESSION['mdp']) {

}
