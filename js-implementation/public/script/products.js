const modal =
    document.getElementById("newProductModal");

const productForm =
    document.querySelector("form");

const tableBody =
    document.querySelector("tbody");


const skuInput =
    productForm.querySelectorAll("input")[0];

const categorySelect =
    productForm.querySelector("select");

const nameInput =
    productForm.querySelectorAll("input")[1];

const descriptionInput =
    productForm.querySelector("textarea");


let editingRow = null;


function openModal() {

    modal.style.display = "flex";
}


function closeModal() {

    modal.style.display = "none";
    productForm.reset();
    editingRow = null;
}


window.addEventListener("click", (event) => {

    if (event.target === modal) {
        closeModal();
    }
});


//Submit
productForm.addEventListener("submit", (event) => {

    event.preventDefault();

    const sku = skuInput.value.trim();
    const category = categorySelect.value.trim();
    const name = nameInput.value.trim();

    if (!sku || !category || !name) {

        alert("Please fill in required fields.");

        return;
    }

    //Edit
    if (editingRow) {

        editingRow.cells[0].textContent = sku;
        editingRow.cells[1].textContent = name;
        editingRow.cells[2].textContent = category;

        alert("Product updated successfully!");
    }

    //Create
    else {

        const row =
            document.createElement("tr");

        row.innerHTML = `
            <td>${sku}</td>

            <td>${name}</td>

            <td>${category}</td>

            <td class="status-active">
                Active
            </td>

            <td>
                <img src="../assets/edit_icon.png" class="action-img edit-btn" alt="Edit">
                <img src="../assets/delete_icon.png" class="action-img delete-btn" alt="Delete">
            </td>
        `;

        tableBody.appendChild(row);

        alert("Product created successfully!");
    }

    setupEditButtons();
    setupDeleteButtons();

    closeModal();
});


//Delete
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


//Editf
function setupEditButtons() {

    const editButtons =
        document.querySelectorAll(".edit-btn");

    editButtons.forEach((button) => {

        button.onclick = function () {

            editingRow =
                this.closest("tr");

            const sku =
                editingRow.cells[0].textContent;

            const name =
                editingRow.cells[1].textContent;

            const category =
                editingRow.cells[2].textContent;

            skuInput.value = sku;
            nameInput.value = name;
            categorySelect.value = category;

            openModal();
        };
    });
}

function setupLogout() {

    const logoutButton =
        document.querySelector(".logout-button");

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

    setupEditButtons();

    setupDeleteButtons();

    setupLogout();
});