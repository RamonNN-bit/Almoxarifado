function mostrarSenha() {
    var inputPassword = document.getElementById("password");
    var button = document.getElementById("mostrarSenhaBtn");
    if (!inputPassword || !button) return;

    var icon = button.querySelector('i');
    if (!icon) return;

    var isPassword = inputPassword.type === "password";
    inputPassword.type = isPassword ? "text" : "password";

    icon.classList.toggle('bi-eye-fill', !isPassword);
    icon.classList.toggle('bi-eye-slash-fill', isPassword);
}

