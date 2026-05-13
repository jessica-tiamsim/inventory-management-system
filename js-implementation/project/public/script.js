const message = document.getElementById('message');

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    login();
});

function login() {
    const usernameField = document.getElementById('username');
    const passwordField = document.getElementById('password');

    fetch('/login', {
        method:'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            username: usernameField .value,
            password: passwordField.value
        })
    })
    .then(res => res.json())
    .then(data => {

        message.classList.remove('success', 'error');
        message.textContent = data.message;

        if (data.message === 'Login successful') {
            message.classList.add('success');    
            setTimeout(() => { window.location.href = 'dashboard.html'; }, 1000);
         } else {
            message.classList.add('error');
        }
    })
    .catch(err => {
        message.textContent = "Connection error";
        message.classList.add('error');
    });
}

    function dashboard() {
        fetch('/dashboard')
        .then(res => res.text())
        .then(data => alert(data));
    }

    function logout() {
        fetch('/logout')
        .then(res => res.json())
        .then(data => alert(data.message));
    }