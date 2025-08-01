// stores/cart.js

import {defineStore} from 'pinia'
import {useAuthStore} from '@/stores/auth'
import api from "@/api.js";


export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        totalPrice: null,
        isLoading: true,
    }),
    getters: {
        totalItems(state) {
            return state.items.reduce((sum, item) => sum + item.quantity, 0);
        },
        getTotalPrice(state) {
            return state.items.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
        },
    },
    actions: {
        async loadCart() {
            const authStore = useAuthStore();
            console.log('load_items', this.items)
            if (authStore.user) {
                const response = await api.get('/carts');
                console.log('load_response', response.data.data);
                this.items = response.data.data.map(item => ({
                    product: item.product,
                    quantity: item.quantity,
                }));
            } else {
                const localItems = JSON.parse(localStorage.getItem('cart_items')) || [];
                this.items = localItems;
            }
            this.isLoading = false;
        },

        async mergeLocalToServer() {
            const authStore = useAuthStore();
            console.log(this.items);
            const localItems = JSON.parse(localStorage.getItem('cart_items')) || [];
            if (authStore.user) {
                for (const item of localItems) {
                    await this.addToCart(item.product, item.quantity);
                }
            }
            localStorage.removeItem('cart_items');
        },

        async addToCart(product, quantity = 1) {
            const authStore = useAuthStore();

            const existing = this.items.find(i => i.product.id === product.id);

            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({product, quantity});
            }

            if (authStore.user) {
                await api.post('/carts', {
                    product_id: product.id,
                    quantity
                });
            } else {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            }
        },

        async updateQuantity(product, quantity) {
            const authStore = useAuthStore();

            if (quantity <= 0) {
                await this.removeFromCart(product);
                return;
            }

            const item = this.items.find(i => i.product.id === product.id);

            if (item) {
                item.quantity = quantity;
            }

            if (authStore.user) {
                await api.put('/carts', {
                    product_id: product.id,
                    quantity
                });
            } else {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            }
        },

        async removeFromCart(product) {
            const authStore = useAuthStore();
            this.items = this.items.filter(item => item.product.id !== product.id);

            if (authStore.user) {
                await api.delete('/carts/' + product.id);
            } else {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            }
        },

        async clearCart() {
            const authStore = useAuthStore();
            this.items = [];

            if (authStore.user) {
                await api.delete('/carts');
            } else {
                localStorage.removeItem('cart_items');
            }
        }
    }
});
