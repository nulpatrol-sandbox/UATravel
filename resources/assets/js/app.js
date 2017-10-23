import Vue from 'vue';
import { sync } from 'vuex-router-sync';
import router from './router';
import store from './store'; // Vuex
import i18n from './lang';
import './bootstrap';
import './components';

import App from './components/App.vue';

store.dispatch('checkLogged');

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.auth) && !store.getters.logged) {
        next({
            name: 'auth.login',
            query: {
                redirect: to.fullPath,
            },
        });
    } else if (to.matched.some(record => record.meta.guest) && store.getters.logged) {
        next({
            name: 'home',
        });
    } else {
        next();
    }
});

sync(store, router);

/* eslint-disable no-new */
new Vue({
    router,
    store,
    i18n,
    el: '#app',
    template: '<App/>',
    components: { App },
});
