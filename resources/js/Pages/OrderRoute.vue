<template>
    <h2 class="text-xl">История заказов:</h2>
    <div v-if="isLoading">
        Загрузка...
    </div>
    <div v-else-if="orders.length === 0">
        Заказов еще нет.
    </div>
    <div v-else class="flex flex-col gap-3">
        <div v-for="order in orders" :key="order.id">
            <OrderItem :order="order"/>
        </div>
        <div v-if="pagination.last_page > 1" class="flex justify-center mt-6 gap-2">
            <button
                @click="fetchOrders(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-1 border rounded cursor-pointer"
            >
                Назад
            </button>
            <button
                v-for="page in pagination.last_page"
                :key="page"
                @click="fetchOrders(page)"
                :disabled="page === pagination.current_page"
                :class="[
            'px-3 py-1 border rounded cursor-pointer',
            page === pagination.current_page ? 'bg-blue-500 text-white' : ''
          ]"
            >
                {{ page }}
            </button>
            <button
                @click="fetchOrders(pagination.current_page + 1)"
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
import OrderItem from "@/components/ui/OrderItem.vue";

export default {
    components: {OrderItem},

    data() {
        return {
            isLoading: true,
            orders: null,
            pagination: {
                current_page: 1,
                last_page: 1,
            },
        }
    },

    mounted() {
        this.fetchOrders();
    },

    methods: {
        async fetchOrders(page = 1) {
            try {
                const response = await api.get(`/orders?page=${page}`);
                this.orders = response.data.data;
                this.isLoading = false
                this.pagination = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                }
            } catch (error) {
                console.error('Ошибка загрузки заказов:', error);
            }
        },
    },
}
</script>
