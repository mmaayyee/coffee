import Vue from 'vue'
import Router from 'vue-router'
import GetCouponActivity from '@/components/page/GetCouponActivity'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/coupon'
    },
    {
      path: '/coupon',
      name: 'GetCouponActivity',
      component: GetCouponActivity
    }
  ]
})
