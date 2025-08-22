<template>
    <div class="" v-if="product">
        <div class="w-full flex gap-4 items-center" v-if="cartItem">
            <button class="cursor-pointer bg-blue-200 rounded-md p-3 w-12 h-12" @click="decrement">−</button>
            <span>{{ cartItem.quantity }}</span>
            <button class="cursor-pointer bg-blue-200 rounded-md p-3 w-12 h-12" @click="increment">+</button>
        </div>
        <button class="text-white cursor-pointer bg-blue-500 rounded-md p-3 min-w-48 h-12" v-else @click="add">Добавить
            в
            корзину
        </button>
    </div>
</template>

<script>
import {useCartStore} from "@/stores/cart.js";

export default {
    name: 'AddToCartButton',
    props: ['product'],

    data() {
        return {
            cartStore: useCartStore(),
        }
    },

    computed: {
        cartItem() {
            return this.cartStore.items.find(item => item.product.id === this.product.id);
        }
    },

    methods: {
        add() {
            this.cartStore.addToCart(this.product);
        },

        increment() {
            this.cartStore.updateQuantity(this.product, this.cartItem.quantity + 1);
        },

        decrement() {
            this.cartStore.updateQuantity(this.product, this.cartItem.quantity - 1);
        }
    },
}
</script>
