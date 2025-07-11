<template>
    <div class="max-w-4xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Результаты поиска</h2>

        <!-- ФИЛЬТРЫ -->
        <div class="flex gap-4 mb-6">
            <select v-model="category" class="border p-2 rounded">
                <option value="">Все категории</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <input type="number" v-model.number="priceMin" placeholder="Цена от" class="border p-2 rounded"/>
            <input type="number" v-model.number="priceMax" placeholder="Цена до" class="border p-2 rounded"/>
        </div>

        <!-- КОНТЕНТ -->
        <div v-if="loading">Загрузка...</div>
        <div v-else-if="products.length === 0">Ничего не найдено</div>
        <div v-else class="grid grid-cols-1 gap-4">
            <div v-for="product in products" :key="product.id">
                <Card :product="product"/>
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

            // Привязка к фильтрам (инициализация из URL позже)
            category: '',
            priceMin: null,
            priceMax: null,

            categoryStore: useCategoryStore(),
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
        // Автоматически подгружаем при изменении URL
        '$route.query': {
            immediate: true,
            handler() {
                this.syncFiltersWithQuery();
                this.fetchResults();
            }
        },

        // Обновляем URL при изменении любого фильтра
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

        async fetchResults() {
            this.loading = true;
            try {
                const response = await api.get('/search/', {
                    params: {
                        text: this.queryText,
                        category: this.category,
                        price_min: this.priceMin,
                        price_max: this.priceMax,
                    }
                });

                this.products = Array.isArray(response.data.data) ? response.data.data : [];
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
