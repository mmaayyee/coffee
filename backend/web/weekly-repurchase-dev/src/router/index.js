import Vue from 'vue'
import Router from 'vue-router'
import WeeklyRepurchase from '@/components/page/WeeklyRepurchase'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/repurchase'
    },
    {
      path: '/repurchase',
      name: 'WeeklyRepurchase',
      component: WeeklyRepurchase
    }
  ]
})
