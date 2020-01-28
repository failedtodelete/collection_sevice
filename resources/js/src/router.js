import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store';
Vue.use(Router)

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    scrollBehavior() {
        return {x: 0, y: 0}
    },
    routes: [

        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: '',
            component: () => import('./layouts/Main.vue'),
            meta: {authRequired: true},
            children: [
                {
                    path: '/',
                    name: 'home', meta: {authRequired: true},
                    component: () => import('./views/pages/home')
                },
                {
                    path: '/users',
                    name: 'users',
                    component: () => import('./views/pages/users/index'),
                    meta: {
                        authRequired: true,
                        breadcrumb: [
                            { title: 'Главная', url: '/' },
                            { title: 'Пользователи', active: true }
                        ],
                        pageTitle: 'Пользователи',
                    },
                },
                {
                    path: '/users/:userId',
                    name: 'users-show',
                    component: () => import('@/views/pages/users/show'),
                    meta: {
                        authRequired: true,
                        breadcrumb: [
                            { title: 'Главная', url: '/' },
                            { title: 'Пользователи', url: '/users' },
                            { title: 'Просмотр', active: true },
                        ],
                        pageTitle: 'Просмотр',
                    },
                }
            ],
        },

        // =============================================================================
        // FULL PAGE LAYOUTS
        // =============================================================================
        {
            path: '',
            component: () => import('@/layouts/FullPage.vue'),
            children: [
                {
                    path: '/login',
                    name: 'login',
                    component: () => import('@/views/Login.vue')
                },
                {
                    path: '/404',
                    name: '404',
                    component: () => import('@/views/Error404.vue')
                },
            ]
        },

        // Redirect to 404 page, if no match found
        {
            path: '*',
            redirect: '/404'
        }
    ],
})

router.afterEach(() => {
    const appLoading = document.getElementById('loading-bg')
    if (appLoading) appLoading.style.display = "none";
})

router.beforeEach((to, from, next) => {
    if (to.meta.authRequired) {
        if (!store.getters['auth/isAuthenticated'])
            router.push({path: '/login', query: {to: to.path}})
    }
    return next()
});

export default router
