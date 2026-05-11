import Vue from 'vue'
import './plugins/axios'
import App from './App.vue'
import router from './router'
import store from './store'
import './plugins/axios'
import './plugins/element'
import './permission'
import './styles/emoji.css'

// global registration OverlayScrollbars
import 'overlayscrollbars/css/OverlayScrollbars.css'
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
Vue.component('overlay-scrollbars', OverlayScrollbarsComponent)

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
