import {createApp} from 'vue';
import './assets/style.css';
import App from './App.vue';
import router from './js/router';
import store from './js/store';
import { showAdminNotice } from './js/admin-notices';

// Ensure wp.i18n is available globally
const { __ } = wp.i18n;

createApp(App).use(router).use(store).mount('#mmvuejs-app');

// Make the showAdminNotice function globally available
window.showAdminNotice = showAdminNotice;