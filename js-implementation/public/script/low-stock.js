// CSV DOWNLOAD
const csvButton =
    document.getElementById("csvButton");

csvButton.addEventListener("click", () => {

    const table =
        document.getElementById("lowStockTable");

    let csv = [];

    for (let row of table.rows) {

        let cols = [];

        for (let cell of row.cells) {

            cols.push(cell.innerText);
        }

        csv.push(cols.join(","));
    }

    const csvFile =
        new Blob([csv.join("\n")], {
            type: "text/csv"
        });

    const downloadLink =
        document.createElement("a");

    downloadLink.download =
        "low-stock-report.csv";

    downloadLink.href =
        window.URL.createObjectURL(csvFile);

    downloadLink.style.display =
        "none";

    document.body.appendChild(downloadLink);

    downloadLink.click();

    document.body.removeChild(downloadLink);
});



// CATEGORY FILTER
const categoryFilter =
    document.getElementById("categoryFilter");

const tableBody =
    document.querySelector(
        "#lowStockTable tbody"
    );

function filterLowStock() {

    const selectedCategory =
        categoryFilter.value.toLowerCase();

    const rows =
        tableBody.querySelectorAll("tr");

    rows.forEach((row) => {

        const category =
            row.cells[2]
                .textContent
                .toLowerCase();

        const matchesCategory =
            selectedCategory === "all" ||
            category === selectedCategory;

        if (matchesCategory) {

            row.style.display = "";

        } else {

            row.style.display = "none";
        }

    });

}

categoryFilter.addEventListener(
    "change",
    filterLowStock
);


// LOGOUT
function setupLogout() {

    const logoutButton =
        document.getElementById("logoutBtn");

    logoutButton.addEventListener("click", () => {

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

document.addEventListener("DOMContentLoaded", () => {

        setupLogout();

    }
);