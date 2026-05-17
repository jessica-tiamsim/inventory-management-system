// CSV DOWNLOAD
const csvButton =
    document.getElementById("csvButton");

csvButton.addEventListener("click", () => {

    const table =
        document.getElementById("valuationTable");

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
        "valuation-report.csv";

    downloadLink.href =
        window.URL.createObjectURL(csvFile);

    downloadLink.click();

});

// SORTING
const sortFilter =
    document.getElementById("sortFilter");

sortFilter.addEventListener("change", () => {

    const table =
        document.querySelector(
            "#valuationTable tbody"
        );

    const rows =
        Array.from(table.rows);

    rows.sort((a, b) => {

        const valueA =
            parseInt(
                a.cells[5]
                    .innerText
                    .replace("₱", "")
            );

        const valueB =
            parseInt(
                b.cells[5]
                    .innerText
                    .replace("₱", "")
            );

        if (sortFilter.value === "highest") {

            return valueB - valueA;

        }

        if (sortFilter.value === "lowest") {

            return valueA - valueB;

        }

        return 0;

    });

    rows.forEach((row) => {

        table.appendChild(row);

    });

});

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