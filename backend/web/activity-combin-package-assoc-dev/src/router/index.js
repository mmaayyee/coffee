import Vue from 'vue'
import Router from 'vue-router'
import CombinationPackage from '@/components/page/CombinationPackage'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/combination'
    },
    {
      path: '/combination',
      name: 'CombinationPackage',
      component: CombinationPackage
    }
  ]
})
