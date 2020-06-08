import Vue from 'vue'
import Router from 'vue-router'
import MonthlyRevenue from '@/components/page/MonthlyRevenue'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/revenue'
    },
    {
      path: '/revenue',
      name: 'MonthlyRevenue',
      component: MonthlyRevenue
    }
  ]
})
