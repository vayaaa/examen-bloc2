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
cancelUserButton.addEventListener("click", () => {
    userModal.close();
});


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