<template>
    <h2 class="text-xl">Товары:</h2>
    <div v-if="isLoading">Загрузка...</div>
    <div v-else>
        <div class="grid-cols-4 grid gap-2">
            <div v-for="product in products" :key="product.id">
                <Card :product="product"/>
            </div>
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
import Card from "@/components/ui/Card.vue";

export default {

    components: {Card},

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
            isLoading: true,
        }
    },
    methods: {
        async fetchProducts(page = 1) {
            this.isLoading = true;
            axios.get(`api/products?page=${page}`).then((response) => {
                console.log(response);
                this.isLoading = false;
                this.products = response.data.data;
                this.pagination = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                }
            }).catch((error) => {
                console.log(error);
            })
        }
    }
}
</script>
