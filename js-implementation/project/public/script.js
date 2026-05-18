let message;
document.addEventListener('DOMContentLoaded', () => {
    message = document.getElementById('message');
});

const loginForm = document.getElementById('loginForm')
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    login();
});
}

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
    return fetch('sidebar.html')
        .then(response => response.text())
        .then(htmlText => {
            const temp = document.createElement('div');
            temp.innerHTML = htmlText;

            const asideComponent = temp.querySelector('aside');
            const headerComponent = temp.querySelector('header');

            if (asideComponent) document.body.insertAdjacentElement('afterbegin', asideComponent);
            if (asideComponent) {
                const mainLayout = document.querySelector('.main');
                if (mainLayout){
                mainLayout.insertAdjacentElement('afterbegin', headerComponent);
                }
            }
        })
            const currentPage = window.location.pathname.split("/").pop();
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

   function viewReport() {
        const viewReportBtn = document.querySelector('.view-report-btn');
        if(viewReportBtn) {
            viewReportBtn.addEventListener('click', () => {
                window.location.href = 'stock_movement.html';
            });
        }
    }

     function product() {
        fetch('product.html')
        .then(res => {
            if (res.status === 401) window.location.href = 'index.html';
            return res.json();
        })
        .then(data => {
            
        });
    }

    function productModal() {
    const modal = document.getElementById('newProductModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeXBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const productForm = document.getElementById('productForm');

    if (openBtn && modal) {
        // Function to easily clear and hide modal overlay elements
        const hideModal = () => {
            modal.classList.remove('show');
            if (productForm) productForm.reset();
        };

        // Open Modal Event
        openBtn.addEventListener('click', () => {
            modal.classList.add('show');
        });

        // Close Triggers
        if (closeXBtn) closeXBtn.addEventListener('click', hideModal);
        if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
        
        // Hide if window outside content box boundary is clicked
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal();
            }
        });

        // Prevent standard submission logic reloads
        if (productForm) {
            productForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // Custom item validation or array pushing data handles go here...
                hideModal();
            });
        }
    }
}

    function logout() {
        fetch('index.html')
        .then(res => res.json())
        .then(data => {
            window.location.href = 'index.html';
        })
    }