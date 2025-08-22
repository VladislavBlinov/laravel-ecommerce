<template>
    <h2 class="text-xl">Товары:</h2>
    <div v-if="isLoading">Загрузка...</div>
    <div v-else>
        <div class="grid-cols-4 grid gap-2">
            <div v-for="product in products" :key="product.id">
                <Card :product="product"/>
            </div>
        </div>
        <div v-if="pagination.last_page > 1" class="flex justify-center mt-6 gap-2">
            <button
                @click="fetchProductsByCategory(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-1 border rounded cursor-pointer"
            >
                Назад
            </button>
            <button
                v-for="page in pagination.last_page"
                :key="page"
                @click="fetchProductsByCategory(page)"
                :disabled="page === pagination.current_page"
                :class="[
            'px-3 py-1 border rounded cursor-pointer',
            page === pagination.current_page ? 'bg-blue-500 text-white' : ''
          ]"
            >
                {{ page }}
            </button>
            <button
                @click="fetchProductsByCategory(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-3 py-1 border rounded cursor-pointer"
            >
                Вперёд
            </button>
        </div>
    </div>
</template>

<script>
import Card from "@/components/ui/ProductCard.vue";
import api from "@/api.js";

export default {
    components: {Card},
    props: ["slug"],
    data() {
        return {
            products: {},
            pagination: {
                current_page: 1,
                last_page: 1,
            },
            isLoading: true,
        }
    },

    mounted() {
        this.fetchProductsByCategory();
    },

    watch: {
        slug(newVal, oldVal) {
            this.fetchProductsByCategory();
        }
    },

    methods: {
        async fetchProductsByCategory(page = 1) {
            this.isLoading = true;
            try {
                const response = await api.get(`/categories/${this.slug}?page=${page}`);
                this.isLoading = false;
                this.products = response.data.data;
                this.pagination = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                }
            } catch (error) {
                console.error('Ошибка загрузки товаров категории:', error);
            }
        }
    }
}
</script>
