// Cart UI Manager
document.addEventListener("DOMContentLoaded", function () {
    // Initialize cart UI
    renderCart();
    setupEventListeners();

    // Subscribe to cart changes
    window.cart.subscribe(() => {
        renderCart();
        updateTotals();
    });
});

function renderCart() {
    const cartItemsContainer = document.getElementById("cart-items");
    const emptyMessage = document.getElementById("empty-cart-message");
    const checkoutBtn = document.getElementById("checkout-btn");

    if (window.cart.cart.length === 0) {
        cartItemsContainer.innerHTML = "";
        emptyMessage.style.display = "block";
        checkoutBtn.disabled = true;
        return;
    }

    emptyMessage.style.display = "none";
    checkoutBtn.disabled = false;

    cartItemsContainer.innerHTML = window.cart.cart
        .map(
            (item) => `
        <div class="flex items-center gap-3 cart-item" data-id="${item.id}">
            <div class="w-12 h-12 bg-gray-50 rounded flex items-center justify-center flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1604908177522-5c0f3b8c1c2f?auto=format&fit=crop&w=200&q=60" class="h-10 object-contain">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">${item.name}</p>
                <p class="text-xs text-gray-500">Rp ${formatNumber(item.price)}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button class="quantity-btn qty-minus" data-id="${item.id}" aria-label="Kurangi">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                </button>
                <span class="text-sm font-medium w-6 text-center">${item.quantity}</span>
                <button class="quantity-btn qty-plus" data-id="${item.id}" aria-label="Tambah">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M6 12h12" />
                    </svg>
                </button>
            </div>
            <button class="remove-item text-red-500 hover:text-red-700 flex-shrink-0" data-id="${item.id}" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    `,
        )
        .join("");

    // Add event listeners for quantity buttons and remove buttons
    document.querySelectorAll(".qty-minus").forEach((btn) => {
        btn.addEventListener("click", () => {
            const itemId = btn.dataset.id;
            const item = window.cart.cart.find((i) => i.id == itemId);
            if (item && item.quantity > 1) {
                window.cart.updateQuantity(itemId, item.quantity - 1);
            } else {
                window.cart.removeItem(itemId);
            }
        });
    });

    document.querySelectorAll(".qty-plus").forEach((btn) => {
        btn.addEventListener("click", () => {
            const itemId = btn.dataset.id;
            const item = window.cart.cart.find((i) => i.id == itemId);
            if (item) {
                window.cart.updateQuantity(itemId, item.quantity + 1);
            }
        });
    });

    document.querySelectorAll(".remove-item").forEach((btn) => {
        btn.addEventListener("click", () => {
            const itemId = btn.dataset.id;
            window.cart.removeItem(itemId);
        });
    });

    updateTotals();
}

function setupEventListeners() {
    // Add to cart buttons
    document.querySelectorAll(".add-to-cart").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = parseInt(btn.dataset.price);

            window.cart.addItem(id, name, price);
        });
    });

    // Checkout button
    document.getElementById("checkout-btn").addEventListener("click", () => {
        if (window.cart.cart.length > 0) {
            alert(
                `Total: Rp ${formatNumber(window.cart.getTotal())}\n\nFitur pembayaran akan segera hadir!`,
            );
        }
    });
}

function updateTotals() {
    const subtotal = window.cart.getSubtotal();
    const tax = window.cart.getTax();
    const total = window.cart.getTotal();

    document.getElementById("subtotal").textContent =
        `Rp ${formatNumber(subtotal)}`;
    document.getElementById("tax").textContent = `Rp ${formatNumber(tax)}`;
    document.getElementById("total").textContent = `Rp ${formatNumber(total)}`;
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
