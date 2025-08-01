<template>
    <div class="max-w-4xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Результаты поиска</h2>

        <div class="flex gap-4 mb-6">
            <select v-model="category" class="border p-2 rounded">
                <option value="">Все категории</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <input type="number" v-model.number="priceMin" placeholder="Цена от" class="border p-2 rounded"/>
            <input type="number" v-model.number="priceMax" placeholder="Цена до" class="border p-2 rounded"/>
        </div>

        <div v-if="loading">Загрузка...</div>
        <div v-else-if="products.length === 0">Ничего не найдено</div>
        <div v-else class="grid grid-cols-1 gap-4">
            <div v-for="product in products" :key="product.id">
                <Card :product="product"/>
            </div>
            <div v-if="pagination.last_page > 1" class="flex justify-center mt-6 space-x-2">
                <button
                    @click="fetchResults(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="px-3 py-1 border rounded cursor-pointer"
                >
                    Назад
                </button>

                <button
                    v-for="page in pagination.last_page"
                    :key="page"
                    @click="fetchResults(page)"
                    :disabled="page === pagination.current_page"
                    :class="[
            'px-3 py-1 border rounded cursor-pointer',
            page === pagination.current_page ? 'bg-blue-500 text-white' : ''
          ]"
                >
                    {{ page }}
                </button>

                <button
                    @click="fetchResults(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 border rounded cursor-pointer"
                >
                    Вперёд
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import api from "@/api";
import {useCategoryStore} from "@/stores/category.js";
import Card from "@/components/ui/Card.vue";

export default {
    name: "SearchRoute",
    components: {Card},

    data() {
        return {
            products: [],
            loading: false,

            category: '',
            priceMin: null,
            priceMax: null,

            categoryStore: useCategoryStore(),

            pagination: {
                current_page: 1,
                last_page: 1,
            },
        };
    },

    computed: {
        categories() {
            return this.categoryStore.categories;
        },
        queryText() {
            return this.$route.query.text || '';
        }
    },

    watch: {
        '$route.query': {
            immediate: true,
            handler() {
                this.syncFiltersWithQuery();
                this.fetchResults();
            }
        },

        category() {
            this.updateQuery();
        },
        priceMin() {
            this.updateQuery();
        },
        priceMax() {
            this.updateQuery();
        }
    },

    methods: {
        syncFiltersWithQuery() {
            const q = this.$route.query;
            this.category = q.category || '';
            this.priceMin = q.price_min ? parseInt(q.price_min) : null;
            this.priceMax = q.price_max ? parseInt(q.price_max) : null;
        },

        updateQuery() {
            this.$router.replace({
                path: '/search',
                query: {
                    text: this.queryText,
                    category: this.category || undefined,
                    price_min: this.priceMin || undefined,
                    price_max: this.priceMax || undefined,
                }
            });
        },

        async fetchResults(page = 1) {
            this.loading = true;
            try {
                const response = await api.get('/search?page=' + page, {
                    params: {
                        text: this.queryText,
                        category: this.category,
                        price_min: this.priceMin,
                        price_max: this.priceMax,
                    }
                });

                this.products = Array.isArray(response.data.data) ? response.data.data : [];
                this.pagination = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                }
            } catch (e) {
                console.error('Ошибка поиска:', e);
                this.products = [];
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
