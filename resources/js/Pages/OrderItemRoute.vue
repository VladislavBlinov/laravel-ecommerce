<template>
    <div v-if="order">
        <div>
            <h2>Заказ номер {{ order.id }}</h2>
            <p>Адрес доставки - {{ order.address }}</p>
        </div>
        <div>
            <p>Статус заказа: {{ order.status }}</p>
            <div>
                <div v-for="item in order.items">
                    <Card :product="item.product"/>
                </div>
            </div>
        </div>


    </div>

</template>
<script>
import api from "@/api.js";

import Card from "@/components/ui/Card.vue";

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
