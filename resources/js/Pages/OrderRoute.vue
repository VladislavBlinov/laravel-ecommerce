<template>
    <h2 class="text-xl">История заказов:</h2>
    <div v-if="isLoading">
        Загрузка...
    </div>
    <div v-else-if="orders.length === 0">
        Заказов еще нет.
    </div>
    <div v-else class="space-y-3">
        <div v-for="order in orders" :key="order.id">
            <OrderItem :order="order"/>
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
        }
    },

    mounted() {
        this.fetchOrders();
    },


    methods: {
        async fetchOrders() {
            const response = await api.get('/orders');
            this.orders = response.data.data;
            this.isLoading = false
        },
    },
}
</script>
