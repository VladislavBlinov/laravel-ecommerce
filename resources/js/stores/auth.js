import {defineStore} from 'pinia';
import api, {getCsrfToken} from '../api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        isLoading: true,
    }),
    actions: {
        async login(credentials) {
            try {
                await getCsrfToken();
                const response = await api.post('/login', credentials);

                this.user = response.data.user;
                this.token = response.data.token;
                localStorage.setItem('token', this.token);
                api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return response.data;
            } catch (error) {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
                console.log(error)
                throw error
            }

        },
        async register(data) {
            try {
                await getCsrfToken();
                const response = await api.post('/register', data);

                this.user = response.data.user;
                this.token = response.data.token;
                localStorage.setItem('token', this.token);
                api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return response.data;
            } catch (error) {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
                delete api.defaults.headers.common['Authorization'];
                throw error
            }
        },

        async logout() {
            try {
                await api.post('/logout');
            } catch (error) {
                console.error('Ошибка выхода:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
                delete api.defaults.headers.common['Authorization'];
            }
        },

        async fetchUser() {
            if (this.token) {
                api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                const response = await api.get('/user');
                this.user = response.data.data;
                this.isLoading = false;
            }
        },

        async updateProfile(data) {
            try {
                const response = await api.put('/user', data)
                this.user = response.data.data
            } catch (error) {
                console.error('Ошибка обновления профиля:', error)
                throw error
            }
        }
    },
});
