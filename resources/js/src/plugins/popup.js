import store from '@/store';

export default {
    install(Vue, options) {
        Vue.prototype.$showPopup = function(name, data) {
            store.dispatch('showPopup', {name, data});
        };
    }
}
