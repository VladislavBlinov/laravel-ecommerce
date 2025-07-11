<template>
    <h2 class="text-xl">Лайки:</h2>
    <div class="space-y-3">
        <div v-for="product in products">
            <LikeItem :product="product"/>
        </div>
    </div>
</template>

<script>
import api from "@/api.js";
import LikeItem from "@/components/ui/LikeItem.vue";


export default {

    components: {LikeItem},

    mounted() {
        this.getProducts()
    },

    data() {
        return {
            products: null,
        }
    },
    methods: {
        async getProducts() {
            const response = await api.get('products/likes');
            this.products = response.data.data;
        }
    }
}
</script>
