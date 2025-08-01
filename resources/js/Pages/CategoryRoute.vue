<template>
    <div v-if="products">
        <div v-for="product in products">
            <Card :product="product"/>
        </div>
        
    </div>
</template>
<script>
import api from "@/api.js";
import Card from "@/components/ui/Card.vue";

export default {
    components: {Card},
    props: ["slug"],
    data() {
        return {
            products: null,
        }

    },
    mounted() {
        this.getCategory();
    },
    watch: {
        slug(newVal, oldVal) {
            this.getCategory();
        }
    },
    methods: {
        async getCategory() {
            if (!this.slug) return;
            try {
                const response = await api.get(`categories/${this.slug}`);
                this.products = response.data.data;
            } catch (error) {
                console.error('Ошибка загрузки товаров категории:', error);
            }
        }
    }
}
</script>
