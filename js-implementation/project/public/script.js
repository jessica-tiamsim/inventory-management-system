let message;
document.addEventListener('DOMContentLoaded', () => {
    message = document.getElementById('message');
});

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

    function loadMenu() {
        fetch('sidebar.html')
        .then(res => response.text())
        .then(data => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');

            const asideComponent = doc.querySelector('aside');
            const headerComponent = doc.querySelector('header');

            document.body.insertAdjacentElement('afterbegin', asideComponent);

            document.querySelector('.main').insertAdjacementElement('afterbegin', headerComponent);
    });
}

    function dashboard() {
        fetch('dashboard.html')
        .then(res => {
            if (res.status === 401) window.location.href = 'index.html';
            return res.json();
        })
        .then(data => {
            
        });
    }

    function logout() {
        fetch('/logout')
        .then(res => res.json())
        .then(data => alert(data.message));
    }