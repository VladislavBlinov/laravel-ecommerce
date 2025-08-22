<template>
    <div v-if="cartItems.length">
        <h2 class="text-xl mb-4">Оформление заказа</h2>
        <div class="flex flex-col gap-4 mb-4">
            <div v-for="item in cartItems" :key="item.product.id">
                {{ item.product.name }} — {{ item.quantity }} × {{ item.product.price }}₽
            </div>
            <p class="font-semibold">Итого: {{ totalPrice }}₽</p>
        </div>
        <label>
            Адрес доставки:
            <input v-model="address" class="border p-2 w-full mt-1"/>
        </label>
        <button @click="submitOrder" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded"
                :disabled="orderStore.isSubmitting || !address">
            {{ orderStore.isSubmitting ? 'Отправка...' : 'Оформить заказ' }}
        </button>
        <p v-if="successMessage" class="text-green-600 mt-2">{{ successMessage }}</p>
    </div>
    <div v-else>
        <p>Корзина пуста.</p>
    </div>
</template>

<script>
import {useCartStore} from '@/stores/cart.js'
import {useOrderStore} from '@/stores/order.js'
import {useAuthStore} from "@/stores/auth.js";

export default {
    data() {
        return {
            cartStore: useCartStore(),
            orderStore: useOrderStore(),
            address: useAuthStore().user.address,
            successMessage: ''
        }
    },

    computed: {
        cartItems() {
            return this.cartStore.items
        },

        totalPrice() {
            return this.cartStore.getTotalPrice
        }
    },

    methods: {
        async submitOrder() {
            try {
                const order = await this.orderStore.createOrder(this.address)
                this.successMessage = 'Заказ успешно создан! Номер заказа: ' + order.id
                this.address = ''
                if (order.status === 'success' && order.url) {
                    window.location.href = order.url;
                } else {
                    this.error = order.message || 'Ошибка при создании платежа';
                }
            } catch (error) {
                console.error('Ошибка при создании заказа', error)
            }
        }
    }
}
</script>
