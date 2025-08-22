<template>
    <div>
        <div v-if="isLoading">Загрузка</div>
        <div v-else>
            <div v-if="isSuccess">
                <p>Заказ номер {{ order.id }}</p>
                <p v-if="order.status==='Оплачен'">оплачен!</p>
                <p v-else>не оплачен!</p>
            </div>
            <div v-else>
                <p>Заказ номер {{ order.id }}</p>
                <p>ожидает оплаты!</p>
            </div>
        </div>
    </div>
</template>

<script>
import api from "@/api.js";
import {useOrderStore} from "@/stores/order.js";

export default {
    data() {
        return {
            isLoading: true,
            isSuccess: false,
            orderStore: useOrderStore(),
            order: null,
        };
    },

    async mounted() {
        this.fetchStatus();
        this.startPolling();
    },

    methods: {
        async fetchStatus() {
            try {
                const orderId = localStorage.getItem("lastOrderId");
                const response = await api.get(`orders/${orderId}`);
                this.order = response.data.data;
                this.isLoading = false;

                if (this.order.status === 'Оплачен' || this.order.status === 'Отменен') {
                    this.isSuccess = true;
                    this.stopPolling();
                }
            } catch (error) {
                console.error('Ошибка загрузки статуса:', error);
            }
        },

        startPolling() {
            this.polling = setInterval(this.fetchStatus, 10000); // каждые 3 секунды
        },

        stopPolling() {
            clearInterval(this.polling);
        },

        beforeUnmount() {
            this.stopPolling();
        }
    }
};
</script>
