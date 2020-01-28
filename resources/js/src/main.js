import Vue from 'vue'
import App from './App.vue'

/** VueBus шина общения */
/**/ import VueBus from 'vue-bus';
/**/ Vue.use(VueBus)

/** Vuesax Component Framework */
/**/ import Vuesax from 'vuesax'
/**/ Vue.use(Vuesax)

/** Axios https requests */
/**/ import axios from "./axios.js"
/**/ Vue.prototype.$http = axios

/** Vue wrapper for hammerjs */
/** Hammer.js wrapper for Vue to support some operation in the mobile */
/**/ import { VueHammer } from 'vue2-hammer'
/**/ Vue.use(VueHammer)

/** Moment js (datetime) */
/**/ import VueMomentLib from 'vue-moment-lib'
/**/ Vue.use(VueMomentLib)

/** VeeValidate */
/**/ import VeeValidate from 'vee-validate'
/**/ Vue.use(VeeValidate)

/** Custom Plugins */
/**/ import PopupPlugin from '@/plugins/popup';
/**/ Vue.use(PopupPlugin)


// Конфигурация темы
import '../themeConfig.js'

// Регистрация глобальных компонентов
import './components.js'

// Vuex Store
import store from './store'

// Vue Router
import router from './router'

// Vue select css
// Note: In latest version you have to add it separately
import 'vue-select/dist/vue-select.css';

// Отключение предупреждений браузера
Vue.config.productionTip = false

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')
