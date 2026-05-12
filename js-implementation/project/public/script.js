document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault();
    login();
});

function login() {
    fetch('/login', {
        method:'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            username: username.value,
            password: password.value
        })
    })
    .then(res => res.json())
    .then(data => {alert(data.message);
        if (data.message === 'Login Successful') {
            window.location.href = '/dashboard'; // Redirect on success
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