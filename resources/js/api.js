import axios from 'axios';

const api = axios.create({
    baseURL: 'https://laravel-ecommerce.loc/api',
    withCredentials: true,
});

export const getCsrfToken = async () => {
    await api.get('/sanctum/csrf-cookie');
};

export default api;
