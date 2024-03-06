import { createApp } from 'vue';

import { createPinia } from 'pinia';

import App from './App.vue';

import router from './router';

// import './css/font.css';
// import './css/main.css';

// import VueAxios from 'vue-axios'
// import axios from "axios";

const app = createApp(App);

app.use(createPinia());

// app.use( VueAxios, axios );

app.use(router);

app.mount('#app');
