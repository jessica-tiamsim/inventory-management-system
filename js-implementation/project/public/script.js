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
            username: usernameField.value,
            password: passwordField.value
        })
    })
    .then(res => res.json())
    .then(data => {alert(data.message);
        // Corrected spelling to match whatever your server sends
        if (data.message === 'Login Successful') { 
            window.location.href = '/dashboard'; 
        }
    })
    .catch(err => console.error("Error:", err));
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