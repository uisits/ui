require('./bootstrap');

require('alpinejs');

import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.config.productionTip = false;

// Initialize and use Vuetify
const vuetifyOptions = {};
Vue.use(Vuetify);

Vue.component('user-impersonate', require('./pages/user/Index.vue').default);
Vue.component('user-feedback', require('./pages/feedback/Index.vue').default);

const app = new Vue({
    el: '#app',
    icons: {
        iconfont: 'mdi',
    },
    vuetify: new Vuetify(vuetifyOptions),
});
