import {createRouter, createWebHistory} from "vue-router";
import {useAuthStore} from '@/stores/auth';
import MainLayout from "@/components/layout/MainLayout.vue";

const routes = [
    {
        path: "/",
        component: MainLayout,
        children: [
            {
                name: "Home",
                path: "",
                component: () => import("@/Pages/HomeRoute.vue"),
            },
            {
                name: "Register",
                path: "/register",
                component: () => import("@/Auth/Register.vue"),
                meta: {
                    guest: true,
                    title: 'Регистрация'
                },
            },
            {
                name: "Login",
                path: "/login",
                component: () => import("@/Auth/Login.vue"),
                meta: {
                    guest: true,
                    title: 'Вход'

                },
            },
            {
                name: "Products",
                path: "/products/:id",

                component: () => import("@/Pages/ProductRoute.vue"),
                props: true,
            },
            {
                name: "Categories",
                path: "/categories/:slug",

                component: () => import("@/Pages/CategoryRoute.vue"),
                props: true,
            },
            {
                name: "Cart",
                path: "/cart",
                component: () => import("@/Pages/CartRoute.vue"),
                meta: {
                    title: 'Корзина'
                }

            },
            {
                name: "Checkout",
                path: "/checkout",

                component: () => import("@/Pages/CheckoutRoute.vue"),

            },
            {
                name: "OrdersLatest",
                path: "/orders/latest",

                component: () => import("@/Pages/OrderReturnRoute.vue"),

            },
            {
                name: "Orders",
                path: "/orders",
                component: () => import("@/Pages/OrderRoute.vue"),
                meta: {
                    title: 'Заказы'
                }

            },
            {
                name: "Order",
                path: "/orders/:id",

                component: () => import("@/Pages/OrderItemRoute.vue"),
                props: true,

            },
            {
                name: "Likes",
                path: "/likes",

                component: () => import("@/Pages/LikeRoute.vue"),
                meta: {
                    title: 'Избранное'
                }

            },
            {
                name: "Search",
                path: "/search/",

                component: () => import("@/Pages/SearchRoute.vue"),
                meta: {
                    title: 'Поиск'
                }

            },
        ]
    },

];


const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const title = to.meta.title;
    if (title) {
        document.title = 'LaraShop: ' + to.meta?.title;
    } else {
        document.title = 'LaraShop';
    }


    if (!authStore.user && authStore.token) {
        await authStore.fetchUser();
    }

    if (to.meta.guest && authStore.user) {
        return next('/');
    }

    if (to.meta.requiresAuth && !authStore.user) {
        return next('/login');
    }

    next();
});

export default router;

