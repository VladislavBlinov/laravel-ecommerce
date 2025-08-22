<template>
    <h2 class="text-xl">Корзина:</h2>
    <div v-if="cartStore.isLoading">
        Загрузка...
    </div>
    <div v-else-if="cartStore.items.length === 0">
        Корзина пуста
    </div>
    <div v-else class="flex flex-col gap-3">
        <div v-for="product in cartStore.items" :key="product.product.id">
            <CartItem :product="product.product"/>
        </div>
        <p>Общая стоимость <span class="text-green-600 font-bold">{{ cartStore.getTotalPrice }} ₽</span></p>
        <button class="self-start cursor-pointer bg-blue-200 rounded-md p-3" @click="clear">Очистить корзину</button>
        <router-link :to="{name:'Checkout'}" class="self-start cursor-pointer bg-green-300 rounded-md p-3">Оформить
            заказ
        </router-link>
    </div>
</template>

<script>
import {useCartStore} from "@/stores/cart.js";
import CartItem from "@/components/ui/CartItem.vue";

export default {
    components: {CartItem},

    created() {
        this.cartStore = useCartStore();
    },

    methods: {
        async clear() {
            try {
                await this.cartStore.clearCart();
            } catch (error) {
                console.error('Ошибка при очистке корзины', error)
            }
        },
    },
}
</script>
