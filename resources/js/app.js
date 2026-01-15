import { createApp } from 'vue';
import App from './components/App.vue'; // Le fichier de Layout principal
import router from './router/router';


createApp(App)
  .use(router)
  .mount('#app');
