import Vue from 'vue'
import Router from 'vue-router'
import MonthlyUserList from '@/components/page/MonthlyUserList'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/userlist'
    },
    {
      path: '/userlist',
      name: 'MonthlyUserList',
      component: MonthlyUserList
    }
  ]
})
