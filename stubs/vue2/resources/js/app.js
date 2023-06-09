import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css' // Ensure you are using css-loader

require('./bootstrap');

window.axios = require('axios');
window.Vue = require('vue');
Vue.config.productionTip = false;

// Initialize Vuetiify
const vuetifyOptions = {};

Vue.use(Vuetify);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
Object.entries(import.meta.globEager('./**/*.vue')).forEach(([path, definition]) => {
    Vue.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
});

const app = new Vue({
    el: '#app',
    icons: {
        iconfont: 'mdi',
    },
    vuetify: new Vuetify(vuetifyOptions),
});
