function mostrarSenha() {
    var inputPassword = document.getElementById("password");
    var eyeIcon = document.getElementById("mostrarSenhaBtn");

    if (inputPassword.type === "password") {
        inputPassword.type = "text";
        eyeIcon.classList.remove('bi-eye-fill');
        eyeIcon.classList.add('bi-eye-slash-fill');
    } else {
        inputPassword.type = "password";
        eyeIcon.classList.remove('bi-eye-slash-fill');
        eyeIcon.classList.add('bi-eye-fill');
    }
}
