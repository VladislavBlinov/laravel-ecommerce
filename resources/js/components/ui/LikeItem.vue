<template>
    <div class="px-2 py-1 w-full flex justify-between">
        <router-link class="flex justify-between"
                     :to="{ name: 'Products', params: { id: product.id } }">
            <img class="rounded-xl" :src="product.image" alt="">
            <h3 class="">{{ product.name }}</h3>
            <p class="text-[#10c44c] font-bold">{{ product.price }} ₽</p>

        </router-link>
        <div>
            <LikeButton :product="product"/>
        </div>
    </div>
</template>
<script>
import LikeButton from "@/components/ui/LikeButton.vue";

export default {
    name: "LikeItem",
    components: {LikeButton},
    props: ["product"],
}
</script>/
