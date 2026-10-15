
const SIDE_CATEGORY = "frites";
const DRINK_CATEGORY = "boissons";

const MAX_QUANTITY = 20;

// Panier
const cartLines = document.getElementById("cart-lines");
const cartEmpty = document.getElementById("cart-empty");
const totalElement = document.getElementById("total");
const submitButton = document.getElementById("submit-button");

// Modale
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modal-title");
const sideChoices = document.getElementById("side-choices");
const drinkChoices = document.getElementById("drink-choices");
const addModalButton = document.getElementById("add-modal");
const cancelModalButton = document.getElementById("cancel-modal");

// Catalogue
const categoryButtons = document.querySelectorAll("#categories-container .item");
const productCards = document.querySelectorAll("#products-container .item");
const categoryTitle = document.getElementById("categorie-title");


//Panier_______________________________________________________________
const cart = [];

// Cherche une ligne identique 
function findLine(type, id, sideId, drinkId) {
    for (const line of cart) {
        let lineSideId = null;
        let lineDrinkId = null;

        if (line.side !== null) {
            lineSideId = line.side.id;
            lineDrinkId = line.drink.id;
        }

        if (line.type === type && line.id === id && lineSideId === sideId && lineDrinkId === drinkId) {
            return line;
        }
    }

    return null;
}


// Ajouter article au panier
function addToCart(type, id, name, price, side, drink) {
    let sideId = null;
    let drinkId = null;

    if (side !== null) {
        sideId = side.id;
        drinkId = drink.id;
    }

    // Existe dans le panier ?
    let line = findLine(type, id, sideId, drinkId);

    // Sinon, on crée sa ligne
    if (line === null) {
        line = { type: type, id: id, name: name, price: price, quantity: 0, side: side, drink: drink };
        cart.push(line);
    }

    if (line.quantity < MAX_QUANTITY) {
        line.quantity = line.quantity + 1;
    }

    renderCart();
}


// Change la quantité
function changeQuantity(line, change) {
    line.quantity = line.quantity + change;

    if (line.quantity <= 0) {
        const position = cart.indexOf(line);
        cart.splice(position, 1);
    } else if (line.quantity > MAX_QUANTITY) {
        line.quantity = MAX_QUANTITY;
    }

    renderCart();
}


// Champ caché pour le PHP 
function addHidden(parent, name, value) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    parent.append(input);
}


//Panier_______________________________________________________________
function renderCart() {
    cartLines.innerHTML = "";
    let total = 0;
    let menuNumber = 0;

    for (const line of cart) {
        total = total + line.price * line.quantity;

        const li = document.createElement("li");
        li.className = "cart-line";

        const name = document.createElement("span");
        name.textContent = line.name;

        // Bouton "-"
        const minus = document.createElement("button");
        minus.type = "button";
        minus.textContent = "−";
        minus.addEventListener("click", function () {
            changeQuantity(line, -1);
        });

        // Quantité
        const quantity = document.createElement("span");
        quantity.textContent = line.quantity;

        // Bouton "+"
        const plus = document.createElement("button");
        plus.type = "button";
        plus.textContent = "+";
        plus.addEventListener("click", function () {
            changeQuantity(line, 1);
        });

        // Prix
        const lineTotal = document.createElement("span");
        lineTotal.textContent = line.price * line.quantity + " €";

        li.append(name, minus, quantity, plus, lineTotal);

        //Options du menus
        if (line.type === "menu") {
            const details = document.createElement("small");
            details.className = "cart-details";
            details.textContent = line.side.name + ", " + line.drink.name;
            li.append(details);

            // Champs cachés d'un menu : menu_lines[0][id], [quantity], [side], [drink]
            const prefix = "menu_lines[" + menuNumber + "]";
            addHidden(li, prefix + "[id]", line.id);
            addHidden(li, prefix + "[quantity]", line.quantity);
            addHidden(li, prefix + "[side]", line.side.id);
            addHidden(li, prefix + "[drink]", line.drink.id);

            menuNumber = menuNumber + 1;
        } else {
            addHidden(li, "products[" + line.id + "]", line.quantity);
        }

        cartLines.append(li);
    }

    totalElement.textContent = total;

    cartEmpty.hidden = cart.length > 0;
    submitButton.disabled = cart.length === 0;
}


// Modale_______________________________________________________________
let currentMenu = null;

function fillChoices(container, categoryName, choiceKey) {
    container.innerHTML = "";

    // Les cartes du catalogue en fonction de leur data-category
    const cards = document.querySelectorAll('#products-container .item[data-category="' + categoryName + '"]');

    for (const card of cards) {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "choice";

        const image = document.createElement("img");
        image.src = card.querySelector("img").getAttribute("src");
        image.alt = "";

        const label = document.createElement("span");
        label.textContent = card.dataset.name; 

        button.append(image, label);


        button.addEventListener("click", function () {
            // currentMenu[choiceKey] : si choiceKey vaut "side", c'est currentMenu.side
            currentMenu[choiceKey] = { id: card.dataset.id, name: card.dataset.name };

            const allChoices = container.querySelectorAll(".choice");

            for (const choice of allChoices) {
                choice.classList.remove("selected");
            }

            button.classList.add("selected");

            // "Ajouter" s'active quand toutes les options sont choisies
            if (currentMenu.side !== null && currentMenu.drink !== null) {
                addModalButton.disabled = false;
            }
        });

        container.append(button);
    }
}


// Ouvre la modale pour un menu
function openMenuModal(card) {
    currentMenu = {
        id: card.dataset.id,
        name: card.dataset.name,
        price: parseFloat(card.dataset.price), 
        side: null,
        drink: null,
    };

    modalTitle.textContent = card.dataset.name;

    fillChoices(sideChoices, SIDE_CATEGORY, "side");
    fillChoices(drinkChoices, DRINK_CATEGORY, "drink");

    addModalButton.disabled = true; 

    modal.showModal(); 
}


// Bouton Ajouter
addModalButton.addEventListener("click", function () {
    addToCart("menu", currentMenu.id, currentMenu.name, currentMenu.price, currentMenu.side, currentMenu.drink);

    modal.close(); // ferme la modale
});


// Bouton Annuler
cancelModalButton.addEventListener("click", function () {
    modal.close();
});


// Catalogue (catégories + articles)_______________________________________________________________
function showCategory(categoryName, label) {
    for (const card of productCards) {
        card.hidden = card.dataset.category !== categoryName;
    }

    for (const button of categoryButtons) {
        if (button.dataset.category === categoryName) {
            button.classList.add("selected");
        } else {
            button.classList.remove("selected");
        }
    }

    categoryTitle.textContent = label;
}


// Clic sur une catégorie
for (const button of categoryButtons) {
    button.addEventListener("click", function () {
        showCategory(button.dataset.category, button.dataset.label);
    });
}


// Clic sur un article 
for (const card of productCards) {
    card.addEventListener("click", function () {
        if (card.dataset.type === "menu") {
            openMenuModal(card);
        } else {
            addToCart("product", card.dataset.id, card.dataset.name, parseFloat(card.dataset.price), null, null);
        }
    });
}


// Chargement de la page_______________________________________________________________
//Affiche la première catégorie
if (categoryButtons.length > 0) {
    showCategory(categoryButtons[0].dataset.category, categoryButtons[0].dataset.label);
}

renderCart();