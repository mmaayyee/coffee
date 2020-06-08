import Vue from 'vue'
import Router from 'vue-router'
import UserSelectionTask from '@/components/page/UserSelectionTask'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/selection'
    },
    {
      path: '/selection',
      name: 'UserSelectionTask',
      component: UserSelectionTask
    }
  ]
})
