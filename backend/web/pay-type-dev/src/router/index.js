import Vue from 'vue'
import Router from 'vue-router'
import PayType from '@/components/PayType'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'PayType',
      component: PayType
    }
  ]
})
