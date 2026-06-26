import Vue from 'vue';
import Vuex from 'vuex';
import {store as storeData} from "../utils/store";
import App from './App.vue';
import VueRouter from 'vue-router'
import route from './routes';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'flowbite';
import commonMixin from "../utils/mixins/commonMixin";
import httpMixin from "../utils/mixins/httpMixin";
import Axios from "axios";

Vue.use(Vuex);
const store = new Vuex.Store(storeData);

Vue.use(VueRouter);
const router = new VueRouter({
    mode: 'history',
    routes: route,
    linkActiveClass: 'active'
});

// Global Axios: attach CSRF token and redirect on 401
Axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
Axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            window.location.href = '/admin';
        }
        return Promise.reject(error);
    }
);

// Router guard: if no authUser, send to login
router.beforeEach((to, from, next) => {
    if (!window.authUser) {
        window.location.href = '/admin';
        return;
    }
    next();
});

Vue.use(Toast, {
    timeout: 3000,
    position: "bottom-right"
});

Vue.mixin(commonMixin);
Vue.mixin(httpMixin);

new Vue({
    el: '#app',
    router, store,
    render: h => h(App)
});