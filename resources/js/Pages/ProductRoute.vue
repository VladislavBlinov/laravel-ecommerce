<template>
    <div v-if="product">
        <div class="flex gap-4">
            <div>
                <img class="rounded-xl max-w-none" :src="product.image" alt="">
            </div>
            <div class="flex flex-col gap-4">
                <h3 class="font-bold">{{ product.name }}</h3>
                <p class="font-light">★ {{ product.rating }}</p>
                <p>{{ product.description }}</p>
                <div>
                    <ReviewList :productId="product.id"/>
                    <ReviewForm :productId="product.id"/>
                </div>
            </div>
            <div class="w-120">
                <div class="w-full flex flex-col gap-3">
                    <div class="w-full flex"><span
                        class="font-bold bg-green-500 text-white p-4 rounded-md">{{ product.price }} ₽</span></div>
                    <div class="flex justify-between gap-4">
                        <AddToCartButton :product="product"/>
                        <LikeButton :product="product"/>
                    </div>
                </div>
            </div>
        </div>
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
                this.product = response.data.data;
                document.title = this.product.name || "LaraShop";
            } catch (error) {
                console.error('Ошибка загрузки товара:', error);
            }
        }
    }
}
</script>
