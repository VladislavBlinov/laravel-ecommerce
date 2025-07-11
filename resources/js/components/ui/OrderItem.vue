<template>
    <div class="px-2 py-1 w-full flex justify-between">
        <router-link class="flex justify-between"
                     :to="{ name: 'Order', params: { id: order.id } }">
            <h3 class="">Заказ №{{ order.id }}</h3>
            <p class="text-[#10c44c] font-bold">Статус - {{ order.status }}</p>
        </router-link>
        <a @click.prevent="repeatOrder(order)" href="#">Повторить заказ</a>

    </div>
</template>
<script>

import {useCartStore} from "@/stores/cart.js";

export default {
    name: "OrderItem",

    props: ["order"],

    methods: {
        repeatOrder(order) {
            const cartStore = useCartStore()
            for (const item of order.items) {
                cartStore.addToCart(item.product, item.quantity)
            }
            this.$router.push({name: 'Checkout'});
        }
    }
}
</script>/
