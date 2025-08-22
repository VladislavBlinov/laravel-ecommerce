<template>
    <div class="w-full flex gap-8 items-center border-1 border-gray-300 p-4 rounded-xl">
        <router-link class="w-full flex justify-between items-center"
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
