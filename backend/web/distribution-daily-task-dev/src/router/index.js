import Vue from 'vue'
import Router from 'vue-router'
import DailyTask from '@/components/page/DailyTask'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/task'
    },
    {
      path: '/task',
      name: 'DailyTask',
      component: DailyTask
    }
  ]
})
