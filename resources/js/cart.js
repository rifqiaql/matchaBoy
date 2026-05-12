// Simple Cart Manager
class CartManager {
    constructor() {
        this.storageKey = "matchaboy_cart";
        this.cart = this.loadCart();
    }

    loadCart() {
        const saved = localStorage.getItem(this.storageKey);
        return saved ? JSON.parse(saved) : [];
    }

    saveCart() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
        this.notifyListeners();
    }

    addItem(id, name, price) {
        const existing = this.cart.find((item) => item.id === id);

        if (existing) {
            existing.quantity += 1;
        } else {
            this.cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1,
            });
        }

        this.saveCart();
    }

    removeItem(id) {
        this.cart = this.cart.filter((item) => item.id !== id);
        this.saveCart();
    }

    updateQuantity(id, quantity) {
        const item = this.cart.find((item) => item.id === id);
        if (item) {
            item.quantity = Math.max(1, quantity);
            this.saveCart();
        }
    }

    getSubtotal() {
        return this.cart.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0,
        );
    }

    getTax() {
        return this.getSubtotal() * 0.1;
    }

    getTotal() {
        return this.getSubtotal() + this.getTax();
    }

    listeners = [];

    subscribe(callback) {
        this.listeners.push(callback);
    }

    notifyListeners() {
        this.listeners.forEach((callback) => callback(this.cart));
    }

    clear() {
        this.cart = [];
        this.saveCart();
    }
}

// Export for use
window.cart = new CartManager();
