import Vue from 'vue'
import Router from 'vue-router'
import DeliveryRegion from '@/components/page/DeliveryRegion'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/region'
    },
    {
      path: '/region',
      name: 'DeliveryRegion',
      component: DeliveryRegion
    }
  ]
})
