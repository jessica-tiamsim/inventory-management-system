// CSV DOWNLOAD
const csvButton =
    document.querySelector(".excel");

csvButton.addEventListener("click", () => {

    const table =
        document.getElementById("movementTable");

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
        "movement-ledger-report.csv";

    downloadLink.href =
        window.URL.createObjectURL(csvFile);

    downloadLink.style.display =
        "none";

    document.body.appendChild(downloadLink);

    downloadLink.click();

    document.body.removeChild(downloadLink);

});


// PRODUCT FILTER
const productFilter =
    document.getElementById("productFilter");

const quantityFilter =
    document.getElementById("quantityFilter");

const tableBody =
    document.querySelector(
        "#movementTable tbody"
    );

function filterTable() {

    const selectedProduct =
        productFilter.value;

    const selectedQuantity =
        quantityFilter.value;

    const rows =
        tableBody.querySelectorAll("tr");

    rows.forEach((row) => {

        const product =
            row.cells[1].textContent;

        const quantity =
            parseInt(
                row.cells[3].textContent
            );

        let matchesProduct =
            selectedProduct === "all" ||
            product === selectedProduct;

        let matchesQuantity = true;

        if (selectedQuantity === "low") {

            matchesQuantity =
                quantity >= 1 &&
                quantity <= 50;

        }

        else if (
            selectedQuantity === "medium"
        ) {

            matchesQuantity =
                quantity >= 51 &&
                quantity <= 150;

        }

        else if (
            selectedQuantity === "high"
        ) {

            matchesQuantity =
                quantity >= 151;

        }

        if (
            matchesProduct &&
            matchesQuantity
        ) {

            row.style.display = "";

        }

        else {

            row.style.display = "none";

        }

    });

}

productFilter.addEventListener(
    "change",
    filterTable
);

quantityFilter.addEventListener(
    "change",
    filterTable
);


// LOGOUT
function setupLogout() {

    const logoutButton =
        document.getElementById("logoutBtn");

    logoutButton.addEventListener("click", () => {

        const confirmLogout =
            confirm(
                "Are you sure you want to logout?"
            );

        if (confirmLogout) {

            alert(
                "Logged out successfully!"
            );

            window.location.href =
                "login.html";

        }

    });

}

document.addEventListener("DOMContentLoaded", () => {

        setupLogout();

    }
);