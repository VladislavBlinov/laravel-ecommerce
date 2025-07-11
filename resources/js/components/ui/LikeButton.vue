<template>
    <div v-if="product && liked!=null">
        <button @click="toggle">
            <svg
                :class="[liked?'hover:fill-red-500/80':'hover:fill-red-500/20']"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                :fill="[liked ? 'red':'none']"
                stroke="#000000"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    d="M20.42 4.58a5.4 5.4 0 00-7.65 0l-.77.78-.77-.78a5.4 5.4 0 00-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"/>
            </svg>
        </button>
    </div>
</template>
<script>
import {useCartStore} from "@/stores/cart.js";
import api from "@/api.js";
import {useAuthStore} from "@/stores/auth.js";

export default {
    name: 'LikeButton',
    props: ['product'],

    data() {
        return {
            liked: null,
            cartStore: useCartStore(),
            authStore: useAuthStore()
        }
    },

    watch: {
        product: {
            immediate: true,
            async handler(product) {
                if (!product?.id) {
                    this.liked = null;
                    return;
                }

                try {
                    if (this.authStore.user) {
                        const response = await api.get(`/products/${product.id}/like`);
                        this.liked = response.data.data;
                    } else {
                        this.liked = false
                    }
                } catch (error) {
                    console.error("Failed to fetch like status:", error);
                    this.liked = false;
                }
            },
        },
    },

    methods: {
        async toggle() {
            if (this.authStore.user) {
                this.liked = !this.liked;
                const response = await api.post(`products/${this.product.id}/like`)
                console.log(response.data)
            } else {
                alert('Сначала войдите!')
            }

        },
    },
}
</script>
