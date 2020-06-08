import Vue from 'vue'
import router from './router'
// import Element from 'element-ui'
// import 'element-ui/lib/theme-chalk/index.css'
import App from './App'
// Vue.use(Element)
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
