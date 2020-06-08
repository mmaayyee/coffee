import Vue from 'vue'
import Router from 'vue-router'
import management from '@/components/management'
Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'management',
      component: management
    }
  ]
})
