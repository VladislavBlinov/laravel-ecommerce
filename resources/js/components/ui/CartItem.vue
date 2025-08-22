<template>
    <div class="w-full flex gap-8 items-center border-1 border-gray-300 p-4 rounded-xl">
        <router-link class="w-full flex justify-between items-center"
                     :to="{ name: 'Products', params: { id: product.id } }">
            <img class="rounded-xl" :src="product.image" alt="">
            <h3 class="">{{ product.name }}</h3>
            <p class="text-[#10c44c] font-bold">{{ product.price }} ₽</p>
        </router-link>
        <AddToCartButton :product="product"/>
    </div>
</template>

<script>
import AddToCartButton from "@/components/ui/AddToCartButton.vue";

export default {
    name: "CartItem",
    components: {AddToCartButton},
    props: ["product"],
}
</script>/
