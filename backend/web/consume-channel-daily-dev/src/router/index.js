import Vue from 'vue'
import Router from 'vue-router'
import ConsumeChannelDaily from '@/components/page/ConsumeChannelDaily'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/channel'
    },
    {
      path: '/channel',
      name: 'ConsumeChannelDaily',
      component: ConsumeChannelDaily
    }
  ]
})
