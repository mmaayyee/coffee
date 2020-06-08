import Vue from 'vue'
import Router from 'vue-router'
import ConsumeDailyTotal from '@/components/page/ConsumeDailyTotal'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/total'
    },
    {
      path: '/total',
      name: 'ConsumeDailyTotal',
      component: ConsumeDailyTotal
    }
  ]
})
