import './bootstrap';
import { createApp } from 'vue'
import App from '@/views/App.vue';
import './bootstrap.js'
import router from '@/router/index.js';


const app = createApp(App);
app.use(router);
app.mount('#app');