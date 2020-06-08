import Vue from 'vue'
import Router from 'vue-router'
import vc from '@/components/page/voiceControl'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/vc'
    },
    {
      path: '/vc',
      name: 'vc',
      component: vc
    }
  ]
})
