import axios from 'axios';

const axiosInstance = axios.create({
    baseURL: '/wp-json/mmvuejs/v1',
    headers: {
        'X-WP-Nonce': mmvuejs.nonce,
    },
});

export default axiosInstance;