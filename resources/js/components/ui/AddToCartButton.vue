<template>
    <div v-if="product">
        <div v-if="cartItem">
            <button @click="decrement">−</button>
            <span>{{ cartItem.quantity }}</span>
            <button @click="increment">+</button>
        </div>
        <button v-else @click="add">Добавить в корзину</button>
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
            console.log(this.cartStore.items);
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
