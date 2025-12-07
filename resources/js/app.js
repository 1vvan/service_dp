import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import uk from 'element-plus/es/locale/lang/uk';
import { moment } from './lib/utils';

const app = createApp(App);

app.config.globalProperties.$moment = moment;

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

app.use(router);
app.use(store);
app.use(ElementPlus, {
    locale: uk,
});

app.mount('#app');
