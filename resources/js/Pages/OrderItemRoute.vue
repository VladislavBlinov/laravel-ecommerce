<template>
    <div v-if="order">
        <h2>Заказ <span class="font-bold">№{{ order.id }}</span></h2>
        <p>Адрес доставки:
            <span class="font-bold">{{ order.address }}</span>
        </p>
        <p>Статус заказа: <span class="font-bold">{{ order.status }}</span></p>
        <p class="mt-2">Товары:</p>
        <div>
            <div v-for="item in order.items">
                <Card :product="item.product"/>
            </div>
        </div>
    </div>
</template>

<script>
import api from "@/api.js";
import Card from "@/components/ui/ProductCard.vue";

export default {
    components: {Card},
    props: ["id"],
    data() {
        return {
            order: null,
        }
    },

    mounted() {
        this.fetchOrder();
    },

    methods: {
        async fetchOrder() {
            if (!this.id) return;
            try {
                const response = await api.get(`orders/${this.id}`);
                this.order = response.data.data;
            } catch (error) {
                console.error('Ошибка загрузки заказа:', error);
            }
        }
    }
}
</script>
