import './bootstrap';
import { createApp } from 'vue'
import App from '@/views/App.vue';
import "../../node_modules/bootstrap/dist/js/bootstrap.js";
import router from '@/router/index.js';


const app = createApp(App);
app.use(router);
app.mount('#app');