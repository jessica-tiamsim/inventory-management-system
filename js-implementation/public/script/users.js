// MODAL
const modal =
    document.getElementById(
        "createAccountModal"
    );

const openBtn =
    document.querySelector(
        ".create-btn"
    );

const closeBtn =
    document.getElementById(
        "closeModal"
    );

// OPEN MODAL
openBtn.addEventListener("click", () => {

    modal.style.display = "flex";

});

// CLOSE MODAL BUTTON
closeBtn.addEventListener("click", () => {

    modal.style.display = "none";

});

// CLOSE WHEN CLICKING OUTSIDE
window.addEventListener("click", (event) => {

    if (event.target === modal) {

        modal.style.display = "none";

    }

});

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