import Vue from 'vue'
import Router from 'vue-router'
import WeeklyRevenue from '@/components/page/WeeklyRevenue'

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
      name: 'WeeklyRevenue',
      component: WeeklyRevenue
    }
  ]
})
