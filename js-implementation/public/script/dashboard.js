const products = [
    {
        name: "Laptop",
        stock: 3,
        threshold: 5,
        price: 35000
    },
    {
        name: "Keyboard",
        stock: 15,
        threshold: 5,
        price: 1200
    },
    {
        name: "Mouse",
        stock: 2,
        threshold: 5,
        price: 800
    },
    {
        name: "Monitor",
        stock: 6,
        threshold: 5,
        price: 8500
    },
    {
        name: "Printer",
        stock: 1,
        threshold: 3,
        price: 12000
    }
];

const recentActivities = [
    "Laptop stock updated (+5)",
    "Mouse stock updated (-2)",
    "New product added: Monitor",
    "Printer stock updated (+1)",
    "Keyboard stock updated (-3)"
];


function loadDashboardStats() {

    const activeProducts = products.length;

    const totalUnits = products.reduce((total, product) => {
        return total + product.stock;
    }, 0);

    const inventoryValue = products.reduce((total, product) => {
        return total + (product.stock * product.price);
    }, 0);

    const lowStockProducts = products.filter(product => {
        return product.stock <= product.threshold;
    });

    document.getElementById("activeProducts").textContent =
        activeProducts;

    document.getElementById("unitsInStock").textContent =
        totalUnits;

    document.getElementById("inventoryValue").textContent =
        `₱${inventoryValue.toLocaleString()}`;

    document.getElementById("lowStockAlerts").textContent =
        lowStockProducts.length;
}


function loadLowStockItems() {

    const lowStockList =
        document.getElementById("lowStockList");

    const lowStockProducts = products.filter(product => {
        return product.stock <= product.threshold;
    });

    lowStockList.innerHTML = "";

    if (lowStockProducts.length === 0) {

        lowStockList.innerHTML = `
            <p class="placeholder-text">
                No low stock products.
            </p>
        `;

        return;
    }

    lowStockProducts.forEach(product => {

        const item = document.createElement("div");

        item.classList.add("list-item");

        item.innerHTML = `
            <div>
                <strong>${product.name}</strong>
            </div>

            <span class="low-stock-badge">
                ${product.stock} left
            </span>
        `;

        lowStockList.appendChild(item);
    });
}


function loadRecentActivities() {

    const recentActivityList =
        document.getElementById("recentActivityList");

    recentActivityList.innerHTML = "";

    recentActivities.forEach(activity => {

        const item = document.createElement("div");

        item.classList.add("list-item");

        item.innerHTML = `
            <p>${activity}</p>
        `;

        recentActivityList.appendChild(item);
    });
}


// Navigation
function setupNavigation() {

    const reportButton =
        document.getElementById("viewReportBtn");

    if (reportButton) {

        reportButton.addEventListener("click", () => {

            window.location.href =
                "valuation.html";
        });
    }

    // Logout
    const logoutBtn =
        document.getElementById("logoutBtn");

    logoutBtn.addEventListener("click", () => {

        const confirmLogout = confirm(
            "Are you sure you want to logout?"
        );

        if (confirmLogout) {

            alert("Logged out successfully!");

            window.location.href =
                "login.html";
        }
    });
}


// Initialize Dashboard
document.addEventListener("DOMContentLoaded", () => {

    loadDashboardStats();

    loadLowStockItems();

    loadRecentActivities();

    setupNavigation();
});