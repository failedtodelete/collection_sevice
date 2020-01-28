import Vue              from 'vue'
import vSelect          from 'vue-select'
import VxCard           from './vendor/VxCard.vue'
import VxList           from './vendor/VxList.vue'
import VxTooltip        from './vendor/VxTooltip.vue'
import FeatherIcon      from './vendor/FeatherIcon.vue'
import VxBreadcrumb     from './vendor/VxBreadcrumb.vue'
import VxInputGroup     from './vendor/VxInputGroup.vue'

Vue.component(VxTooltip.name, VxTooltip)
Vue.component(VxCard.name, VxCard)
Vue.component(VxList.name, VxList)
Vue.component(VxBreadcrumb.name, VxBreadcrumb)
Vue.component(FeatherIcon.name, FeatherIcon)
Vue.component(VxInputGroup.name, VxInputGroup)

/**
 * Настройка компонента v-select по умолчанию.
 */
vSelect.props.components.default = () => ({
  Deselect: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'XIcon',
        svgClasses: 'w-4 h-4 mt-1'
      }
    }),
  },
  OpenIndicator: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'ChevronDownIcon',
        svgClasses: 'w-5 h-5'
      }
    }),
  },
});

Vue.component(vSelect)
