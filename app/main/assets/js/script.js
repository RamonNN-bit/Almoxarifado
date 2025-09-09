document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');


    if (localStorage.getItem('email') && localStorage.getItem('password')) {
        emailInput.value = localStorage.getItem('email');
        passwordInput.value = localStorage.getItem('password');
        rememberCheckbox.checked = true;
} else {
        emailInput.value = '';
        passwordInput.value = '';
        rememberCheckbox.checked = false;
    }
    // Lida com o envio do formulário
    loginForm.addEventListener('submit', (e) => {

        if (rememberCheckbox.checked) {
            localStorage.setItem('email', emailInput.value);
            localStorage.setItem('password', passwordInput.value);
        } else {
            localStorage.removeItem('email');
            localStorage.removeItem('password');
        }

        console.log('Email:', emailInput.value);
        console.log('Senha:', passwordInput.value);
        console.log('Lembrar-me:', rememberCheckbox.checked);


    });
});
