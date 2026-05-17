const modal =
    document.getElementById("recordMovementModal");

const movementForm =
    document.querySelector("form");

const tableBody =
    document.querySelector("tbody");

const productSelect =
    movementForm.querySelectorAll("select")[0];

const typeSelect =
    movementForm.querySelectorAll("select")[1];

const quantityInput =
    movementForm.querySelector("input[type='number']");

const notesInput =
    movementForm.querySelector("textarea");

function openModal() {

    modal.style.display = "flex";
}

function closeModal() {

    modal.style.display = "none";

    movementForm.reset();
}

window.addEventListener("click", (event) => {

    if (event.target === modal) {

        closeModal();
    }
});

movementForm.addEventListener("submit", (event) => {

    event.preventDefault();

    const product =
        productSelect.value;

    const type =
        typeSelect.value;

    const quantity =
        quantityInput.value;

    const notes =
        notesInput.value.trim();

    if (
        !product ||
        product === "Select a product..." ||
        !type ||
        !quantity
    ) {

        alert("Please fill in all required fields.");

        return;
    }

    const currentDate =
        new Date().toISOString().split("T")[0];

    const recordedBy =
        "Alex Reyes";

    const formattedType =
        type === "inbound"
            ? "Stock-In"
            : type === "outbound"
            ? "Stock-Out"
            : "Adjustment";

    const row =
        document.createElement("tr");

    row.innerHTML = `
        <td>${currentDate}</td>

        <td>${product}</td>

        <td>${formattedType}</td>

        <td>${quantity}</td>

        <td>${notes || "-"}</td>

        <td>${recordedBy}</td>
    `;

    tableBody.prepend(row);

    alert("Stock movement recorded successfully!");

    closeModal();
});

function setupLogout() {

    const logoutButton =
        document.getElementById("logoutBtn");

    if (logoutButton) {

        logoutButton.addEventListener("click", () => {

            const confirmLogout = confirm(
                "Are you sure you want to logout?"
            );

            if (confirmLogout) {

                window.location.href =
                    "login.html";
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {

    setupLogout();

});