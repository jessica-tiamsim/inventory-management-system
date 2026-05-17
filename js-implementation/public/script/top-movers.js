const csvButton =
    document.querySelector(".excel");

csvButton.addEventListener("click", () => {

    const table =
        document.getElementById("topMoversTable");

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
        "top-movers-report.csv";

    downloadLink.href =
        window.URL.createObjectURL(csvFile);

    downloadLink.style.display =
        "none";

    document.body.appendChild(downloadLink);

    downloadLink.click();

    document.body.removeChild(downloadLink);
});

// Logout
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