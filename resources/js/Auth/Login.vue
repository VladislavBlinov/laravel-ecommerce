<template>
    <form @submit.prevent="login" class="w-full" action="#" method="POST">
        <!-- Контейнер для ошибок сервера -->
        <div v-if="serverErrors.length"
             class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                <li v-for="(error, index) in serverErrors" :key="index">{{ error }}</li>
            </ul>
        </div>

        <div>
            <label for="email">Email</label>
            <input
                class="p-2 border-1 rounded-md w-full"
                :class="{ 'border-red-500': errors.email }"
                type="email"
                id="email"
                v-model="form.email"
                @input="validateField('email')"
            />
            <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</span>
        </div>

        <div>
            <label for="password">Пароль</label>
            <input
                class="p-2 border-1 rounded-md w-full"
                :class="{ 'border-red-500': errors.password }"
                type="password"
                id="password"
                v-model="form.password"
                @input="validateField('password')"
            />
            <span v-if="errors.password" class="text-red-500 text-sm">{{ errors.password }}</span>
        </div>

        <button
            class="cursor-pointer bg-blue-600 p-1.5 rounded-3xl text-white"
            :disabled="isLoading || !isFormValid"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading || !isFormValid }"
        >
            {{ isLoading ? 'Загрузка...' : 'Войти' }}
        </button>
    </form>
</template>

<script>
import {useAuthStore} from '../stores/auth';
import {useCartStore} from "@/stores/cart.js";

export default {
    name: "Login",
    data() {
        return {
            form: {
                email: '',
                password: '',
            },
            errors: {
                email: '',
                password: '',
            },
            serverErrors: [],
            isLoading: false,
        };
    },
    computed: {
        isFormValid() {
            return !this.errors.email && !this.errors.password &&
                this.form.email && this.form.password;
        }
    },
    methods: {
        validateField(field) {
            if (field === 'email') {
                if (!this.form.email) {
                    this.errors.email = 'Email обязателен';
                } else if (!this.isValidEmail(this.form.email)) {
                    this.errors.email = 'Введите корректный Email';
                } else {
                    this.errors.email = '';
                }
            }
            if (field === 'password') {
                if (!this.form.password) {
                    this.errors.password = 'Пароль обязателен';
                } else if (this.form.password.length < 6) {
                    this.errors.password = 'Пароль должен быть не менее 6 символов';
                } else {
                    this.errors.password = '';
                }
            }
        },
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        validateForm() {
            this.validateField('email');
            this.validateField('password');
            return !this.errors.email && !this.errors.password;
        },
        async login() {
            this.serverErrors = [];

            if (!this.validateForm()) {
                return;
            }

            this.isLoading = true;
            const authStore = useAuthStore();
            const cartStore = useCartStore();

            try {
                await authStore.login(this.form);
                await cartStore.mergeLocalToServer();
                this.$router.push('/');
            } catch (error) {
                if (error.response && error.response.data) {
                    if (error.response.data.errors) {
                        this.serverErrors = Object.values(error.response.data.errors).flat();
                    } else if (error.response.data.message) {
                        this.serverErrors = [error.response.data.message];
                    } else {
                        this.serverErrors = ['Произошла ошибка при входе'];
                    }
                } else {
                    this.serverErrors = ['Не удалось подключиться к серверу'];
                }
            } finally {
                this.isLoading = false;
            }
        },
    }
}
</script>
