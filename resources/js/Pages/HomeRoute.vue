<template>
    <h2 class="text-xl">Товары:</h2>
    <div class="grid-cols-4 grid gap-2">
        <div v-for="product in products">
            <Card :product="product"/>
        </div>
    </div>
</template>

<script>
import Card from "@/components/ui/Card.vue";

export default {

    components: {Card},

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
            axios.get('api/products').then((response) => {
                console.log(response);
                this.products = response.data.data;
            }).catch((error) => {
                console.log(error);
            })
        }
    }
}
</script>
