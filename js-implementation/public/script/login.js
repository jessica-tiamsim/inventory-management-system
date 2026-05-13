document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const user = document.getElementById('userIn').value;
    const pass = document.getElementById('passIn').value;


    if(user === "jc_admin@prism.com" && pass === "password1217") {
        window.location.href = "dashboard.html";
    } else {
        alert("The login credentials don't match an account in the system.");
    }
});