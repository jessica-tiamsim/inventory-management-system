let message;


document.addEventListener('DOMContentLoaded', () => {
    message = document.getElementById('message');
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            login();
        });
    }
});

function login() {
    const usernameField = document.getElementById('username');
    const passwordField = document.getElementById('password');

    if (!usernameField || !passwordField) {
        console.error("Could not find username or password input fields!");
        return;
    }

    fetch('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            username: usernameField.value,
            password: passwordField.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if(message) {
            message.classList.remove('success', 'error');
            message.textContent = data.message;
        }

        if (data.message === 'Login successful') {
            if(message) message.classList.add('success');    
            setTimeout(() => { window.location.href = 'dashboard.html'; }, 1000);
         } else {
            if(message) message.classList.add('error');
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        if(message) {
            message.textContent = "Connection error";
            message.classList.add('error');
        }
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

        const currentPage = window.location.pathname.split("/").pop();

        const reportPages = [
            "low_stock.html",
            "valuation.html",
            "movement_ledger.html",
            "top_movers.html"
        ]

        document.querySelectorAll("nav a").forEach(link => {
        link.classList.remove("active");
        });

        if (reportPages.includes(currentPage)) {
            const reportLink = document.getElementById("nav-report");

            if (reportLink) {
                reportLink.classList.add("active");
            }
        } else {

        document.querySelectorAll("nav a").forEach(link => {
                if (link.getAttribute("href") === currentPage) {
                    link.classList.add("active");
                }
        
            });
        }

        const titleMap = {
            "dashboard.html": "DASHBOARD",
            "products.html": "PRODUCT",
            "stock_movement.html": "STOCK MOVEMENT",
            "low_stock.html": "REPORTS / LOW STOCK",
            "valuation.html": "REPORTS / VALUATION",
            "movement_ledger.html": "REPORTS / MOVEMENT LEDGER",
            "top_movers.html": "REPORTS / TOP MOVERS",
            "users.html": "USER"
        }

        const headTitle = document.getElementById("headTitle");
        if (headTitle) {
            headTitle.textContent = `PRISM / INVENTORY MANAGEMENT / ${titleMap [currentPage] || "DASHBOARD"}`;
        }
        

        const logout = document.getElementById("logoutBtn");
        if (logout) {
            logout.addEventListener("click", () => {
                window.location.href = "index.html";
            });
        }
    })
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
                window.location.href = 'low-stock.html';
            });
        }
    }

     function product() {
        fetch('products.html')
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
    function stockModal() {
        const modal = document.getElementById('movementModal');
        const openBtn = document.querySelector('.record');
        const closeBtn = document.getElementById('closeModalBtn');
        const stockForm = document.getElementById('movementForm');

        if (!openBtn || !modal) return;

        const hideModal = () => {
            modal.classList.remove('show');
            if (stockForm) stockform.reset();
        };

        openBtn.addEventListener('click', () => {
            modal.classList.add('show');
        });

        if (closeBtn) closeBtn.addEventListener('click', hideModal);

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal();
            }
        });

        if (stockForm) {
            stockForm.addEventListener('submit', (e) => {
                e.preventDefault();
                hideModal();
            });
        }
    }

    /*Report Nav*/
    document.addEventListener("DOMContentLoaded", () => {
        const currentPage = window.location.pathname.split("/").pop();
        const tabs = document.querySelectorAll(".tab-choices a.tab");

        tabs.forEach(link => {
            const pageLink = link.getAttribute("href");

            if (currentPage === pageLink) {
                link.classList.add("current");
            }
        });
    });