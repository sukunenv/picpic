import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);

    const totalItems = computed(() =>
        items.value.reduce((sum, item) => sum + item.quantity, 0)
    );

    const totalPrice = computed(() =>
        items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    );

    function addItem(menuItem) {
        const existing = items.value.find(i => i.id === menuItem.id);
        if (existing) {
            existing.quantity++;
        } else {
            items.value.push({
                id: menuItem.id,
                name: menuItem.name,
                price: menuItem.price,
                image: menuItem.image,
                category: menuItem.category,
                quantity: 1,
                note: '',
            });
        }
    }

    function removeItem(id) {
        items.value = items.value.filter(i => i.id !== id);
    }

    function increaseQty(id) {
        const item = items.value.find(i => i.id === id);
        if (item) item.quantity++;
    }

    function decreaseQty(id) {
        const item = items.value.find(i => i.id === id);
        if (item) {
            if (item.quantity <= 1) removeItem(id);
            else item.quantity--;
        }
    }

    function updateNote(id, note) {
        const item = items.value.find(i => i.id === id);
        if (item) item.note = note;
    }

    function clearCart() {
        items.value = [];
    }

    return {
        items,
        totalItems,
        totalPrice,
        addItem,
        removeItem,
        increaseQty,
        decreaseQty,
        updateNote,
        clearCart,
    };
});
