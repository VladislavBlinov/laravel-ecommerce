import axios from 'axios';

window.axios = axios;

window.axios.defaults.baseURL = 'https://laravel-ecommerce.loc';
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
