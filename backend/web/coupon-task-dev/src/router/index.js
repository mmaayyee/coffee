import Vue from 'vue'
import Router from 'vue-router'
import CouponSendTask from '@/components/CouponSendTask'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'CouponSendTask',
      component: CouponSendTask
    }
  ]
})
