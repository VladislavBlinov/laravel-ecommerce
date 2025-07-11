<template>
    <div v-if="product">
        <div><img class="rounded-xl" :src="product.image" alt="">
            <h3 class="">{{ product.name }}</h3>
            <h3 class="">{{ product.description }}</h3>
            <p class="text-[#10c44c] font-bold">{{ product.price }} ₽</p></div>
        <p>{{ product.rating }}</p>
        <LikeButton :product="product"/>
        <AddToCartButton :product="product"/>
        <ReviewForm :productId="product.id"/>
        <ReviewList :productId="product.id"/>
    </div>

</template>
<script>
import api from "@/api.js";
import AddToCartButton from "@/components/ui/AddToCartButton.vue";
import LikeButton from "@/components/ui/LikeButton.vue";
import ReviewForm from "@/components/ui/ReviewForm.vue";
import ReviewList from "@/components/ui/ReviewList.vue";

export default {
    components: {ReviewList, ReviewForm, LikeButton, AddToCartButton},
    props: ["id"],
    data() {
        return {
            product: null,
        }
    },
    mounted() {
        this.getProduct();

    },
    watch: {
        id(newVal, oldVal) {
            this.getProduct();

        }
    },
    methods: {
        async getProduct() {
            if (!this.id) return;
            try {
                const response = await api.get(`products/${this.id}`);
                console.log('PRODUCT', response.data.data);
                this.product = response.data.data;
                document.title = this.product.name || "LaraShop";
            } catch (error) {
                console.error('Ошибка загрузки товара:', error);
            }
        }
    }
}
</script>
