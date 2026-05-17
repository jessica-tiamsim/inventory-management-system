const modal =
    document.getElementById("newProductModal");

const productForm =
    document.querySelector("form");

const tableBody =
    document.querySelector("tbody");


// INPUTS
const inputs =
    productForm.querySelectorAll("input");

const skuInput = inputs[0];

const nameInput = inputs[1];

const unitPriceInput = inputs[2];

const unitCostInput = inputs[3];

const reorderThresholdInput = inputs[4];

const supplierNameInput = inputs[5];

const categorySelect =
    productForm.querySelector("select");

const descriptionInput =
    productForm.querySelector("textarea");


let editingRow = null;


// OPEN MODAL
function openModal() {

    modal.style.display = "flex";
}


// CLOSE MODAL
function closeModal() {

    modal.style.display = "none";

    productForm.reset();

    editingRow = null;
}


// CLOSE WHEN CLICKING OUTSIDE
window.addEventListener("click", (event) => {

    if (event.target === modal) {

        closeModal();
    }
});


// SUBMIT
productForm.addEventListener("submit", (event) => {

    event.preventDefault();

    const sku =
        skuInput.value.trim();

    const name =
        nameInput.value.trim();

    const description =
        descriptionInput.value.trim();

    const category =
        categorySelect.value.trim();

    const unitPrice =
        unitPriceInput.value.trim();

    const unitCost =
        unitCostInput.value.trim();

    const reorderThreshold =
        reorderThresholdInput.value.trim();

    const supplierName =
        supplierNameInput.value.trim();


    // REQUIRED FIELDS
    if (!sku || !name || !category) {

        alert("Please fill in required fields.");

        return;
    }


    // EDIT
    if (editingRow) {

        editingRow.cells[0].textContent = sku;
        editingRow.cells[1].textContent = name;
        editingRow.cells[2].textContent = description;
        editingRow.cells[3].textContent = category;
        editingRow.cells[4].textContent = `₱${unitPrice}`;
        editingRow.cells[5].textContent = `₱${unitCost}`;
        editingRow.cells[6].textContent = reorderThreshold;
        editingRow.cells[7].textContent = supplierName;

        alert("Product updated successfully!");
    }


    // CREATE
    else {

        const row =
            document.createElement("tr");

        row.innerHTML = `
            <td>${sku}</td>

            <td>${name}</td>

            <td>${description}</td>

            <td>${category}</td>

            <td>₱${unitPrice}</td>

            <td>₱${unitCost}</td>

            <td>${reorderThreshold}</td>

            <td>${supplierName}</td>

            <td class="status-active">
                Active
            </td>

            <td>
                <img
                    src="../assets/edit_icon.png"
                    class="action-img edit-btn"
                    alt="Edit"
                >

                <img
                    src="../assets/delete_icon.png"
                    class="action-img delete-btn"
                    alt="Delete"
                >
            </td>
        `;

        tableBody.appendChild(row);

        alert("Product created successfully!");
    }


    setupEditButtons();

    setupDeleteButtons();

    closeModal();
});


// DELETE
function setupDeleteButtons() {

    const deleteButtons =
        document.querySelectorAll(".delete-btn");

    deleteButtons.forEach((button) => {

        button.onclick = function () {

            const confirmDelete = confirm(
                "Are you sure you want to delete this product?"
            );

            if (confirmDelete) {

                this.closest("tr").remove();

                alert("Product deleted.");
            }
        };
    });
}


// EDIT
function setupEditButtons() {

    const editButtons =
        document.querySelectorAll(".edit-btn");

    editButtons.forEach((button) => {

        button.onclick = function () {

            editingRow =
                this.closest("tr");


            // GET VALUES
            const sku =
                editingRow.cells[0].textContent;

            const name =
                editingRow.cells[1].textContent;

            const description =
                editingRow.cells[2].textContent;

            const category =
                editingRow.cells[3].textContent;

            const unitPrice =
                editingRow.cells[4]
                    .textContent
                    .replace("₱", "");

            const unitCost =
                editingRow.cells[5]
                    .textContent
                    .replace("₱", "");

            const reorderThreshold =
                editingRow.cells[6].textContent;

            const supplierName =
                editingRow.cells[7].textContent;


            // PUT BACK TO FORM
            skuInput.value = sku;

            nameInput.value = name;

            descriptionInput.value = description;

            categorySelect.value = category;

            unitPriceInput.value = unitPrice;

            unitCostInput.value = unitCost;

            reorderThresholdInput.value =
                reorderThreshold;

            supplierNameInput.value =
                supplierName;


            openModal();
        };
    });
}


// LOGOUT
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


// INITIALIZE
document.addEventListener("DOMContentLoaded", () => {

    setupEditButtons();

    setupDeleteButtons();

    setupLogout();
});


const searchInput =
    document.getElementById("searchInput");

const categoryFilter =
    document.getElementById("categoryFilter");

const statusFilter =
    document.getElementById("statusFilter");

function filterProducts() {

    const searchValue =
        searchInput.value.toLowerCase();

    const categoryValue =
        categoryFilter.value.toLowerCase();

    const statusValue =
        statusFilter.value.toLowerCase();

    const rows =
        tableBody.querySelectorAll("tr");

    rows.forEach((row) => {

        const sku =
            row.cells[0].textContent.toLowerCase();

        const productName =
            row.cells[1].textContent.toLowerCase();

        const description =
            row.cells[2].textContent.toLowerCase();

        const category =
            row.cells[3].textContent.toLowerCase();

        const status =
            row.cells[8].textContent.toLowerCase();

        const matchesSearch =
            sku.includes(searchValue) ||
            productName.includes(searchValue) ||
            description.includes(searchValue);

        const matchesCategory =
            categoryValue === "all" ||
            category === categoryValue;

        const matchesStatus =
            statusValue === "all" ||
            status === statusValue;

        if (
            matchesSearch &&
            matchesCategory &&
            matchesStatus
        ) {

            row.style.display = "";

        } else {

            row.style.display = "none";
        }
    });
}

searchInput.addEventListener(
    "input",
    filterProducts
);

categoryFilter.addEventListener(
    "change",
    filterProducts
);

statusFilter.addEventListener(
    "change",
    filterProducts
);