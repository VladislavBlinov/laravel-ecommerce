<template>
    <h2 class="text-xl">Корзина:</h2>
    <div v-if="cartStore.isLoading">
        Загрузка...
    </div>
    <div v-else-if="cartStore.items.length === 0">
        Корзина пуста.
    </div>
    <div v-else class="space-y-3">
        <div v-for="product in cartStore.items" :key="product.product.id">
            <CartItem :product="product.product"/>
        </div>
        <p>Общая стоимость - {{ cartStore.getTotalPrice }}</p>
        <button @click="clear">Очистить корзину</button>
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
            await this.cartStore.clearCart();
        },
    },
}
</script>
