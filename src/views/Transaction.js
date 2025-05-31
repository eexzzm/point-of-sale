//Handler for Transaction page

import Breadcumb from "../components/Breadcumb.js";

// --- GLOBAL VARIABLES & ELEMENT SELECTION ---
const breadcumbEl = document.querySelector("[data-root=breadcumb]");

// Form elements for adding item
const selectItemsEl = document.getElementById("nameOfItem"); // Changed to getElementById
const selectedItemIdEl = document.getElementById("selectedItemId");
const itemQuantityEl = document.getElementById("itemQuantity");
const currentPricePerUnitEl = document.getElementById("currentPricePerUnit");
const addItemToCartBtn = document.getElementById("addItemToCartBtn");

// Cart display elements
const cartItemsTableBody = document.getElementById("cartItemsTableBody");
const cartSubtotalEl = document.getElementById("cartSubtotal");
const clearCartBtn = document.getElementById("clearCartBtn");

// Payment summary elements
const discountInputEl = document.getElementById("discountInput");
const totalPaymentEl = document.getElementById("totalPayment");
const cashInputEl = document.getElementById("cashInput");
const moneyChangesOutputEl = document.getElementById("moneyChangesOutput");
const transactionForm = document.getElementById("transactionForm"); // The main form

// --- CART STATE ---
let cartItems = []; // Array to hold items in the cart
let currentItemPrice = 0; // Price of the currently selected item in the dropdown

// --- INITIAL RENDER ---
Breadcumb(breadcumbEl, { home: "../../", current: "Transaction" });
renderCart(); // Render empty cart initially

// --- HELPER FUNCTIONS ---

// Function to format number to IDR currency
const formatRupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(number);
};

// Function to calculate subtotal for a single item
const calculateItemSubtotal = (quantity, price) => {
    return quantity * price;
};

// Function to calculate overall cart totals
const calculateCartTotals = () => {
    let subtotal = 0;
    cartItems.forEach(item => {
        subtotal += item.subtotal;
    });

    let discountPercentage = parseFloat(discountInputEl.value) || 0;
    if (discountPercentage < 0) discountPercentage = 0;
    if (discountPercentage > 100) discountPercentage = 100; // Cap discount at 100%

    let discountAmount = 0;
    // Apply discount logic: 10% if subtotal >= 500,000 (example)
    const MIN_AMOUNT_FOR_DISCOUNT = 500000; // Define your discount threshold
    const DISCOUNT_PERCENTAGE = 10; // Define your discount percentage

    if (subtotal >= MIN_AMOUNT_FOR_DISCOUNT) {
        discountAmount = (subtotal * DISCOUNT_PERCENTAGE) / 100;
        discountInputEl.value = DISCOUNT_PERCENTAGE; // Auto-fill discount % if criteria met
        // Optionally, make discountInputEl readonly if auto-applied
        // discountInputEl.readOnly = true;
    } else {
        // If below threshold or cleared, ensure discount is 0 and input is editable
        discountAmount = 0;
        discountInputEl.value = 0;
        // discountInputEl.readOnly = false;
    }

    let totalPayment = subtotal - discountAmount;
    if (totalPayment < 0) totalPayment = 0; // Ensure total doesn't go negative

    let cashPaid = parseInt(cashInputEl.value.replace(/\D/g,'')) || 0; // Remove non-digits
    let moneyChanges = cashPaid - totalPayment;

    // Update UI elements
    cartSubtotalEl.textContent = formatRupiah(subtotal);
    totalPaymentEl.value = formatRupiah(totalPayment);
    moneyChangesOutputEl.value = formatRupiah(moneyChanges);
};


// Function to render/re-render cart items in the table
const renderCart = () => {
    cartItemsTableBody.innerHTML = ''; // Clear existing items

    if (cartItems.length === 0) {
        cartItemsTableBody.innerHTML = `<tr><td colspan="6" class="text-center">No items in cart</td></tr>`;
    } else {
        cartItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary btn-sm decrease-qty" data-index="${index}">-</button>
                        <input type="number" class="form-control form-control-sm text-center cart-item-qty" value="${item.quantity}" min="1" data-index="${index}">
                        <button type="button" class="btn btn-outline-secondary btn-sm increase-qty" data-index="${index}">+</button>
                    </div>
                </td>
                <td>${formatRupiah(item.price)}</td>
                <td>${formatRupiah(item.subtotal)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            cartItemsTableBody.appendChild(row);
        });
    }
    calculateCartTotals(); // Recalculate totals every time cart renders
};

// --- EVENT LISTENERS ---

// 1. When an item is selected from the dropdown
selectItemsEl.addEventListener("change", e => {
    const value = e.target.value;
    if (value === "") { // If "--Choose item" is selected
        selectedItemIdEl.value = "";
        currentPricePerUnitEl.value = "";
        currentItemPrice = 0;
        itemQuantityEl.value = 1;
        return;
    }
    const [itemId, itemName, pricePerUnit] = value.split("@");
    selectedItemIdEl.value = itemId;
    currentPricePerUnitEl.value = formatRupiah(parseFloat(pricePerUnit)); // Format for display
    currentItemPrice = parseFloat(pricePerUnit); // Store as number for calculation
    itemQuantityEl.value = 1; // Default quantity to 1
});

// 2. When "Add to Cart" button is clicked
addItemToCartBtn.addEventListener("click", () => {
    const itemId = selectedItemIdEl.value;
    const itemName = selectItemsEl.options[selectItemsEl.selectedIndex].text; // Get name from dropdown text
    const quantity = parseInt(itemQuantityEl.value);

    if (!itemId || isNaN(quantity) || quantity <= 0) {
        alert("Please select an item and enter a valid quantity.");
        return;
    }

    const price = currentItemPrice; // Use the stored numeric price

    // Check if item already exists in cart
    const existingItemIndex = cartItems.findIndex(item => item.id === itemId);

    if (existingItemIndex > -1) {
        // Update quantity of existing item
        cartItems[existingItemIndex].quantity += quantity;
        cartItems[existingItemIndex].subtotal = calculateItemSubtotal(
            cartItems[existingItemIndex].quantity,
            cartItems[existingItemIndex].price
        );
    } else {
        // Add new item to cart
        cartItems.push({
            id: itemId,
            name: itemName,
            quantity: quantity,
            price: price,
            subtotal: calculateItemSubtotal(quantity, price)
        });
    }

    renderCart(); // Update display
    // Reset add item form
    selectItemsEl.value = "";
    selectedItemIdEl.value = "";
    currentPricePerUnitEl.value = "";
    itemQuantityEl.value = 1;
    currentItemPrice = 0; // Reset price
});

// 3. Delegation for changing item quantity in cart or removing item
cartItemsTableBody.addEventListener("click", e => {
    const index = e.target.dataset.index;
    if (index === undefined) return; // Not a relevant button click

    if (e.target.classList.contains("remove-item") || e.target.closest(".remove-item")) {
        cartItems.splice(index, 1); // Remove item from array
    } else if (e.target.classList.contains("increase-qty")) {
        cartItems[index].quantity++;
        cartItems[index].subtotal = calculateItemSubtotal(cartItems[index].quantity, cartItems[index].price);
    } else if (e.target.classList.contains("decrease-qty")) {
        if (cartItems[index].quantity > 1) {
            cartItems[index].quantity--;
            cartItems[index].subtotal = calculateItemSubtotal(cartItems[index].quantity, cartItems[index].price);
        } else {
            // Optionally remove if quantity drops to 0, or just prevent going below 1
            cartItems.splice(index, 1); // Remove if quantity becomes 0
        }
    }
    renderCart(); // Re-render cart after changes
});

// For direct input into quantity field in cart table (if any)
cartItemsTableBody.addEventListener("change", e => {
    if (e.target.classList.contains("cart-item-qty")) {
        const index = e.target.dataset.index;
        let newQuantity = parseInt(e.target.value);
        if (isNaN(newQuantity) || newQuantity < 1) {
            newQuantity = 1; // Default to 1 if invalid
        }
        cartItems[index].quantity = newQuantity;
        cartItems[index].subtotal = calculateItemSubtotal(cartItems[index].quantity, cartItems[index].price);
        renderCart();
    }
});


// 4. When "Clear Cart" button is clicked
clearCartBtn.addEventListener("click", () => {
    if (confirm("Are you sure you want to clear the entire cart?")) {
        cartItems = []; // Empty the cart array
        renderCart(); // Update display
        // Reset payment summary inputs as well
        discountInputEl.value = 0;
        cashInputEl.value = 0;
        calculateCartTotals();
    }
});

// 5. When Discount, Cash input changes
discountInputEl.addEventListener("input", calculateCartTotals); // Use 'input' event for real-time update
cashInputEl.addEventListener("input", () => {
    // Clean input by removing non-numeric characters for calculation
    cashInputEl.value = cashInputEl.value.replace(/\D/g, ''); 
    calculateCartTotals();
});

// 6. Form Submission (Send cart data to backend)
transactionForm.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent default form submission

    const transactionId = document.getElementById("transactionId").value; // Get the manual transaction ID

    if (!transactionId) {
        alert("Please enter a Transaction ID.");
        return;
    }
    if (cartItems.length === 0) {
        alert("Cart is empty. Please add items.");
        return;
    }

    // Prepare data for submission
    const totalPayment = parseFloat(totalPaymentEl.value.replace(/\D/g,'')) || 0; // Clean formatted number
    const cashPaid = parseFloat(cashInputEl.value.replace(/\D/g,'')) || 0; // Clean formatted number
    const moneyChanges = parseFloat(moneyChangesOutputEl.value.replace(/-|\D/g,'')) || 0; // Clean and handle negative if any
    const discountAmount = parseFloat(discountInputEl.value) || 0; // This is a percentage now, convert to actual amount in backend
    
    // Recalculate subtotal for sending to backend (best practice to recalculate on backend too)
    let subtotalBeforeDiscount = 0;
    cartItems.forEach(item => {
        subtotalBeforeDiscount += item.subtotal;
    });

    // Data to send
    const formData = new FormData();
    formData.append('transactionIdTxt', transactionId);
    formData.append('cartItems', JSON.stringify(cartItems.map(item => ({
        item_id: item.id,
        quantity: item.quantity,
        item_price: item.price // Send the snapshot price
    }))));
    formData.append('subtotalBeforeDiscount', subtotalBeforeDiscount); // Send subtotal
    formData.append('discountPercentage', discountAmount); // Send percentage
    formData.append('totalPayment', totalPayment); // Send total Payment
    formData.append('cashPaid', cashPaid);
    formData.append('moneyChanges', moneyChanges);
    formData.append('process_transaction', true); // Indicate this is the main submit button

    try {
        const response = await fetch(transactionForm.action, {
            method: 'POST',
            body: formData // Use FormData for traditional POST-like behavior, or JSON.stringify for JSON body
            // If sending JSON, you'd use headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
        });

        const result = await response.text(); // Read as text to see full PHP output
        // console.log(result); // Log for debugging

        // Check for redirect in the response (common in PHP for success/failure)
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            // Handle cases where PHP might return JSON or just text without redirect
            // For now, assuming it redirects based on original code structure
            alert("Transaction processing response received, but no redirect: " + result);
        }

    } catch (error) {
        console.error("Error processing transaction:", error);
        alert("An error occurred during transaction. Please check console.");
    }
});

// Reset logic
document.getElementById("resetFormBtn").addEventListener("click", () => {
    cartItems = [];
    renderCart(); // Reset cart display
    selectItemsEl.value = "";
    selectedItemIdEl.value = "";
    currentPricePerUnitEl.value = "";
    itemQuantityEl.value = 1;
    currentItemPrice = 0;
    discountInputEl.value = 0;
    cashInputEl.value = 0;
    calculateCartTotals(); // Reset all totals and money changes
    document.getElementById("transactionId").value = ""; // Clear transaction ID
});