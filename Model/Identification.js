document.getElementById('togglePassword').addEventListener('click', function () {
    var passwordField = document.getElementById("password");

    // Bascule entre les types "password" et "text"
    if (passwordField.type === "password") {
        passwordField.type = "text";
        // Change l'icône en œil barré
        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        passwordField.type = "password";
        // Remet l'icône en œil ouvert
        this.innerHTML = '<i class="fas fa-eye"></i>';
    }
});
