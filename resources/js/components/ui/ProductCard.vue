<template>
    <router-link class="px-2 py-1 block w-full" :to="{ name: 'Products', params: { id: product.id } }">
        <img class="rounded-xl" :src="product.image" alt="">
        <h3 class="">{{ product.name }}</h3>
        <p class="text-[#10c44c] font-bold">{{ product.price }} ₽</p>
    </router-link>
</template>

<script>
export default {
    name: "Card",
    props: ["product"],
}
</script>/
