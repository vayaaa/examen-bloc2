//Icon modifier produit
const editButtons = document.querySelectorAll(".modifier-product");

// Modale produit
const modal = document.getElementById("modal");
const productForm = document.getElementById("product-form");
const idInput = document.getElementById("product-id");
const nameInput = document.getElementById("product-name");
const descriptionInput = document.getElementById("product-description");
const priceInput = document.getElementById("product-price");
const imageInput = document.getElementById("product-image");
const availableInput = document.getElementById("product-available");

const cancelButton = document.getElementById("cancel-modal");
const saveButton = document.getElementById("save-modal");

// icon modifier menus
const menuEditButtons = document.querySelectorAll(".modifier-menu");

// Modale menus
const menuModal = document.getElementById("menu-modal");
const menuForm = document.getElementById("menu-form");
const menuIdInput = document.getElementById("menu-id");
const menuNameInput = document.getElementById("menu-name");
const menuDescriptionInput = document.getElementById("menu-description");
const menuPriceInput = document.getElementById("menu-price");
const menuImageInput = document.getElementById("menu-image");
const menuAvailableInput = document.getElementById("menu-available");
const menuOptionInputs = document.querySelectorAll('#menu-form input[name="options[]"]');

const cancelMenuButton = document.getElementById("cancel-menu-modal");


// icon modifier user
const userEditButtons = document.querySelectorAll(".modifier-user");

// Modale user
const userModal = document.getElementById("user-modal");
const userForm = document.getElementById("user-form");
const userIdInput = document.getElementById("user-id");
const userNameInput = document.getElementById("user-firstname");
const userLastNameInput = document.getElementById("user-lastname");
const userRoleInput = document.getElementById("user-role");

const cancelUserButton = document.getElementById("cancel-user-modal");




// Modale PRODUCT_______________________________________________________________
editButtons.forEach((button) => {
    button.addEventListener("click", () => {
        //Récupération des données
        idInput.value = button.dataset.id;
        nameInput.value = button.dataset.name;
        descriptionInput.value = button.dataset.description;
        priceInput.value = button.dataset.price;
        imageInput.value = button.dataset.image;
        availableInput.value = button.dataset.available;

        modal.showModal();
    });
});

// Bouton Annuler
if (cancelButton) {
    cancelButton.addEventListener("click", () => {
        modal.close();
    });
}


if (productForm) {
    productForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(productForm);

        try {
            const response = await fetch("index.php?page=admin&section=products&action=update-product",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de modifier le produit"
                );
            }

            const button = document.querySelector('.modifier-product[data-id="' + CSS.escape(idInput.value) + '"]');

            button.dataset.name = nameInput.value;
            button.dataset.description = descriptionInput.value;
            button.dataset.price = priceInput.value;
            button.dataset.image = imageInput.value;
            button.dataset.available = availableInput.value;

            // 7. Mettre à jour les cellules du tableau sans recharger
            const row = button.closest("tr");

            row.querySelector("td:nth-child(1) img").src = "./assets/img/products/" + imageInput.value;
            row.querySelector("td:nth-child(1) img").alt = nameInput.value;
            row.querySelector("td:nth-child(2)").textContent = nameInput.value;
            row.querySelector("td:nth-child(4)").textContent =
                Number(priceInput.value).toFixed(2) + " €";

            row.querySelector("td:nth-child(5)").textContent =
                availableInput.value === "1"
                    ? "Disponible"
                    : "Indisponible";

            modal.close();
            alert("Produit modifié avec succès !");

        } catch (error) {

            alert(error.message);

        }
    });

}


// Modale USER_______________________________________________________________
userEditButtons.forEach((button) => {
    button.addEventListener("click", () => {
        userIdInput.value = button.dataset.id;
        userNameInput.value = button.dataset.firstname;
        userLastNameInput.value = button.dataset.lastname;
        userRoleInput.value = button.dataset.role;

        userModal.showModal();
    });
});

//bouton annuler
if (cancelUserButton) {
    cancelUserButton.addEventListener("click", () => {
        userModal.close();
    });
}

if (userForm) {
    userForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(userForm);

        try {
            const response = await fetch("index.php?page=admin&section=users&action=update-user",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();


            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de modifier l'utilisateur"
                );
            }

            const button = document.querySelector(`.modifier-user[data-id="' + CSS.escape(userIdInput.value) + '"]`);

            button.dataset.firstname = userNameInput.value;
            button.dataset.lastname = userLastNameInput.value;
            button.dataset.role = userRoleInput.value;

            // MAJ du tableau
            const row = button.closest("tr");

            row.querySelector("td:nth-child(1)").textContent = userNameInput.value;
            row.querySelector("td:nth-child(2)").textContent = userLastNameInput.value;
            row.querySelector("td:nth-child(4)").textContent = userRoleInput.options[userRoleInput.selectedIndex].text;

            userModal.close();

            alert("Utilisateur modifié avec succès!");

        } catch (error) {

            alert(error.message);

        }
    });
}

// Modale MENUS_______________________________________________________________
menuEditButtons.forEach((button) => {
    button.addEventListener("click", () => {
        menuIdInput.value = button.dataset.id;
        menuNameInput.value = button.dataset.name;
        menuDescriptionInput.value = button.dataset.description;
        menuPriceInput.value = button.dataset.price;
        menuImageInput.value = button.dataset.image;
        menuAvailableInput.value = button.dataset.available;

        let selectedOptions = [];

        try {
            selectedOptions = JSON.parse(button.dataset.options || "[]");
        } catch (error) {
            console.error("Options du menu invalides", error);
        }

        // Cocher les options qui appartiennent déjà au menu.
        menuOptionInputs.forEach((input) => {
             input.checked = true;
            // input.checked = selectedOptions.includes(Number(input.value));
        });

        menuModal.showModal();
    });
});

//bouton annuler
if (cancelMenuButton) {
    cancelMenuButton.addEventListener("click", () => {
        menuModal.close();
    });
}


if (menuForm) {
    menuForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(menuForm);

        try {
            const response = await fetch("index.php?page=admin&section=menus&action=update-menu",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();


            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de modifier le menu"
                );
            }

            const button = document.querySelector(
                `.modifier-menu[data-id="${CSS.escape(menuIdInput.value)}"]`
            );

            button.dataset.name = menuNameInput.value;
            button.dataset.description = menuDescriptionInput.value;
            button.dataset.price = menuPriceInput.value;
            button.dataset.image = menuImageInput.value;
            button.dataset.available = menuAvailableInput.value;
            // Mémoriser les nouvelles options pour la prochaine ouverture
            const selectedOptions = [];

            menuOptionInputs.forEach((input) => {
                if (input.checked) {
                    selectedOptions.push(Number(input.value));
                }
            });
            button.dataset.options = JSON.stringify(selectedOptions);

            // MAJ du tableau
            const row = button.closest("tr");

            row.querySelector("td:nth-child(1) img").src = "./assets/img/products/" + menuImageInput.value;
            row.querySelector("td:nth-child(1) img").alt = menuNameInput.value;
            row.querySelector("td:nth-child(2)").textContent = menuNameInput.value;
            row.querySelector("td:nth-child(3)").textContent =
                Number(menuPriceInput.value).toFixed(2) + " €";

            row.querySelector("td:nth-child(4)").textContent =
                menuAvailableInput.value === "1"
                    ? "Disponible"
                    : "Indisponible";


            menuModal.close();

            alert("Menu modifié avec succès!");

        } catch (error) {

            alert(error.message);

        }
    });
}