<template>
    <form @submit.prevent="register" class="w-full" action="#" method="POST">
        <div v-if="serverErrors.length"
             class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                <li v-for="(error, index) in serverErrors" :key="index">{{ error }}</li>
            </ul>
        </div>

        <div>
            <label for="name">Имя</label>
            <input
                class="p-2 border-1 rounded-md w-full"
                :class="{ 'border-red-500': errors.name }"
                type="text"
                id="name"
                v-model="form.name"
                @input="validateField('name')"
            />
            <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
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
            <label for="address">Адрес</label>
            <input
                class="p-2 border-1 rounded-md w-full"
                type="text"
                id="address"
                v-model="form.address"
            />
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

        <div>
            <label for="password_confirmation">Пароль повторно</label>
            <input
                class="p-2 border-1 rounded-md w-full"
                :class="{ 'border-red-500': errors.password_confirmation }"
                type="password"
                id="password_confirmation"
                v-model="form.password_confirmation"
                @input="validateField('password_confirmation')"
            />
            <span v-if="errors.password_confirmation" class="text-red-500 text-sm">{{
                    errors.password_confirmation
                }}</span>
        </div>

        <button
            class="cursor-pointer bg-blue-600 p-1.5 rounded-3xl text-white"
            :disabled="isLoading || !isFormValid"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading || !isFormValid }"
        >
            {{ isLoading ? 'Загрузка...' : 'Зарегистрироваться!' }}
        </button>
    </form>
</template>

<script>
import {useAuthStore} from '../stores/auth';
import {useCartStore} from "@/stores/cart.js";

export default {
    name: "Register",
    data() {
        return {
            form: {
                name: '',
                email: '',
                address: '',
                password: '',
                password_confirmation: '',
            },
            errors: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            serverErrors: [],
            isLoading: false,
        };
    },
    computed: {
        isFormValid() {
            return !this.errors.name &&
                !this.errors.email &&
                !this.errors.password &&
                !this.errors.password_confirmation &&
                this.form.name &&
                this.form.email &&
                this.form.password &&
                this.form.password_confirmation;
        }
    },
    methods: {
        validateField(field) {
            if (field === 'name') {
                if (!this.form.name) {
                    this.errors.name = 'Имя обязательно';
                } else if (this.form.name.length < 2) {
                    this.errors.name = 'Имя должно содержать не менее 2 символов';
                } else {
                    this.errors.name = '';
                }
            }
            if (field === 'email') {
                if (!this.form.email) {
                    this.errors.email = 'Email обязателен';
                } else if (!this.isValidEmail(this.form.email)) {
                    this.errors.email = 'Введите корректный email';
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
                this.validateField('password_confirmation');
            }
            if (field === 'password_confirmation') {
                if (!this.form.password_confirmation) {
                    this.errors.password_confirmation = 'Подтверждение пароля обязательно';
                } else if (this.form.password !== this.form.password_confirmation) {
                    this.errors.password_confirmation = 'Пароли не совпадают';
                } else {
                    this.errors.password_confirmation = '';
                }
            }
        },
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        validateForm() {
            this.validateField('name');
            this.validateField('email');
            this.validateField('password');
            this.validateField('password_confirmation');
            return !this.errors.name &&
                !this.errors.email &&
                !this.errors.password &&
                !this.errors.password_confirmation;
        },
        async register() {
            this.serverErrors = [];

            if (!this.validateForm()) {
                return;
            }

            this.isLoading = true;
            const authStore = useAuthStore();
            const cartStore = useCartStore();

            try {

                await authStore.register(this.form);
                await cartStore.mergeLocalToServer();
                this.$router.push('/');
            } catch (error) {
                if (error.response && error.response.data) {
                    if (error.response.data.errors) {
                        this.serverErrors = Object.values(error.response.data.errors).flat();
                    } else if (error.response.data.message) {
                        this.serverErrors = [error.response.data.message];
                    } else {
                        this.serverErrors = ['Произошла ошибка при регистрации'];
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
