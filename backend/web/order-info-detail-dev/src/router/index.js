import Vue from 'vue'
import Router from 'vue-router'
import OrderInfoDetail from '@/components/page/OrderInfoDetail'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/detail'
    },
    {
      path: '/detail',
      name: 'OrderInfoDetail',
      component: OrderInfoDetail
    }
  ]
})
