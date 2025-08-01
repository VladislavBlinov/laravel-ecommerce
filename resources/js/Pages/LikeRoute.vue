<template>
    <h2 class="text-xl">Лайки:</h2>
    <div class="space-y-3">
        <div v-for="product in products">
            <LikeItem :product="product"/>
        </div>
        <div v-if="pagination.last_page > 1" class="flex justify-center mt-6 space-x-2">
            <button
                @click="fetchProducts(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-1 border rounded cursor-pointer"
            >
                Назад
            </button>

            <button
                v-for="page in pagination.last_page"
                :key="page"
                @click="fetchProducts(page)"
                :disabled="page === pagination.current_page"
                :class="[
            'px-3 py-1 border rounded cursor-pointer',
            page === pagination.current_page ? 'bg-blue-500 text-white' : ''
          ]"
            >
                {{ page }}
            </button>

            <button
                @click="fetchProducts(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-3 py-1 border rounded cursor-pointer"
            >
                Вперёд
            </button>
        </div>
    </div>
</template>

<script>
import api from "@/api.js";
import LikeItem from "@/components/ui/LikeItem.vue";


export default {

    components: {LikeItem},

    mounted() {
        this.fetchProducts()
    },

    data() {
        return {
            products: {},
            pagination: {
                current_page: 1,
                last_page: 1,
            },
        }
    },
    methods: {
        async fetchProducts(page = 1) {
            const response = await api.get('likes?page=' + page);
            this.products = response.data.data;
            this.pagination = {
                current_page: response.data.meta.current_page,
                last_page: response.data.meta.last_page,
            }
        }
    }
}
</script>
