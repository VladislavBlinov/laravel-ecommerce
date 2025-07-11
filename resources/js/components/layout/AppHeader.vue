<template>
    <header class="w-full max-w-250 mx-auto bg-white p-4 rounded-b-2xl">
        <div class="flex justify-between space-x-4 items-center">
            <router-link class="text-blue-500 bold text-2xl" :to="{name:'Home'}">LaraShop
            </router-link>
            <button @click.prevent="categoryToggle"
                    class="bg-blue-600 rounded-xl text-white cursor-pointer px-4 py-2 hover:bg-blue-500">
                Каталог
            </button>
            <form @submit.prevent="search" class="bg-blue-600 rounded-xl flex flex-1">
                <input class="flex-1 bg-white border-2 border-blue-600 rounded-xl p-1.5" v-model="text" name="text"
                       type="text"
                       placeholder="Искать">
                <button class="cursor-pointer flex-0 bg-blue-600 rounded-xl text-white hover:bg-blue-500" type="submit">
                    Поиск
                </button>
            </form>
            <nav class="flex justify-between">
                <div class="space-x-2">
                    <div class="space-x-2" v-if="!authStore.user">
                        <router-link :to="{name:'Login'}" active-class="text-red-500">Вход
                        </router-link>
                        <router-link :to="{name:'Register'}" active-class="text-red-500">Регистрация
                        </router-link>
                    </div>
                    <div v-if="authStore.user" class="relative " @mouseover="userShow=true"
                         @mouseleave="userShow=false">
                        <div class="flex flex-col items-center cursor-pointer">
                            <UserSvg class="fill-red-500 w-4 h-4 hover:fill-red-500"/>
                            <span class="text-[#001a34]/40">{{ authStore.user.name }}</span>
                        </div>
                        <div v-if="userShow"
                             class="absolute p-2 rounded-md bg-white shadow-lg ring-1 ring-black/5">
                            <ul>
                                <li>
                                    <router-link :to="{name:'Cart'}" active-class="text-red-500">Корзина
                                    </router-link>
                                </li>
                                <li>
                                    <router-link :to="{name:'Orders'}" active-class="text-red-500">Заказы
                                    </router-link>
                                </li>
                                <li>
                                    <router-link :to="{name:'Likes'}" active-class="text-red-500">Избранное
                                    </router-link>
                                </li>
                                <li>
                                    <button class="cursor-pointer" v-if="authStore.user" @click="handleLogout">Выйти
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>


                </div>
            </nav>
        </div>
        <div v-if="categoryShow" class="space-x-2 mt-2">
            <router-link v-if="categoryStore.categories" v-for="category in categoryStore.categories" :to="{ name: 'Categories', params: {
                    slug: category.slug } }" active-class="text-red-500">
                {{ category.name }}
            </router-link>
        </div>
    </header>
</template>
<script>
import {useAuthStore} from "@/stores/auth.js";
import router from "@/router.js";
import {useCategoryStore} from "@/stores/category.js";
import UserSvg from "@/components/ui/UserSvg.vue";

export default {
    name: 'AppHeader',
    components: {UserSvg},
    data() {
        return {
            categoryStore: useCategoryStore(),
            text: '',
            categoryShow: false,
            userShow: false
        }
    },

    created() {
        this.authStore = useAuthStore();
        this.categoryStore.loadCategories();
    },

    methods: {
        async handleLogout() {
            const authStore = useAuthStore();
            await authStore.logout();
            this.$router.push('/');
        },

        async search() {
            if (this.text !== '') {
                router.push({path: '/search/', query: {text: this.text}});
            }
        },

        async categoryToggle() {
            this.categoryShow = !this.categoryShow;
        },


    },


}
</script>
