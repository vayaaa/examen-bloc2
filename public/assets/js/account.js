// Modale MY ACCOUNT_______________________________________________________________
const accountModal = document.getElementById("account-modal");
const openAccountButton = document.getElementById("open-account");
const accountForm = document.getElementById("account-form");
const accountFirstNameInput = document.getElementById("account-firstname");
const accountLastNameInput = document.getElementById("account-lastname");
const passwordForm = document.getElementById("password-form");
const deleteForm = document.getElementById("delete-form");

const headerName = document.getElementById("header-name");

const cancelAccountButton = document.getElementById("cancel-account-modal");


// Ouvrir la modale
if (openAccountButton) {
    openAccountButton.addEventListener("click", () => {
        accountModal.showModal();
    });
};


//bouton annuler
if (cancelAccountButton) {
    cancelAccountButton.addEventListener("click", () => {
        accountForm.reset();
        accountModal.close();
    });
}

// Modifier nom et prénom
if (accountForm) {
    accountForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(accountForm);

        try {
            const response = await fetch("index.php?page=account&action=update-profile",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();


            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de modifier mes informations"
                );
            }

            // MAJ du header sans recharger
            headerName.textContent = accountFirstNameInput.value;

            // Les nouvelles valeurs deviennent les valeurs d'origine (pour le bouton Annuler)
            accountFirstNameInput.defaultValue = accountFirstNameInput.value;
            accountLastNameInput.defaultValue = accountLastNameInput.value;

            accountModal.close();

            alert("Utilisateur modifié avec succès!");

        } catch (error) {

            alert(error.message);

        }
    });
}


// Changer mon mot de passe
if (passwordForm) {
    passwordForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(passwordForm);

        try {
            const response = await fetch("index.php?page=account&action=change-password",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();


            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de modifier le mot de passe"
                );
            }


            passwordForm.reset();
            accountModal.close();

            alert("Mot de passe modifié avec succès!");

        } catch (error) {

            alert(error.message);

        }
    });
}



// Supprimer mes données
if (deleteForm) {
    deleteForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!confirm("Supprimer définitivement votre compte ?")) {
            return;
        }

        const formData = new FormData(deleteForm);

        try {
            const response = await fetch("index.php?page=account&action=delete-account",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();


            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Impossible de supprimer le compte"
                );
            }

            alert("Votre compte a été supprimé.");
            window.location.href = "index.php?page=login";

        } catch (error) {
            alert(error.message);
        }
    });
}