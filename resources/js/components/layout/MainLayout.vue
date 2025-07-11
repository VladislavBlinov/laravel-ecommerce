<template>
    <div class="flex flex-col h-screen gap-y-3">
        <AppHeader/>
        <main class="flex-1 w-full max-w-250 mx-auto bg-white p-4 rounded-2xl">
            <router-view/>
        </main>
        <Footer class="flex-0"/>
    </div>
</template>

<script>
import AppHeader from "@/components/layout/AppHeader.vue";
import Footer from "@/components/layout/Footer.vue";
import {useAuthStore} from "@/stores/auth.js";
import {useCartStore} from "@/stores/cart.js";


export default {
    components: {Footer, AppHeader},

    async created() {
        const authStore = useAuthStore()
        const cartStore = useCartStore()
        await authStore.fetchUser();
        await cartStore.loadCart()
    },
};
</script>
