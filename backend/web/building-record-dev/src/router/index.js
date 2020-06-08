import Vue from 'vue'
import Router from 'vue-router'
import buildRecord from '@/components/page/buildRecord'
import buildDetail from '@/components/page/buildDetail'
import buildList from '@/components/page/buildList'
import modifyContact from '@/components/page/modifyContact'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/list'
    },
    {
      path: '/build',
      name: 'buildRecord',
      component: buildRecord
    },
    {
      path: '/detail',
      name: 'buildDetail',
      component: buildDetail
    },
    {
      path: '/list',
      name: 'buildList',
      component: buildList
    },
    {
      path: '/contact',
      name: 'modifyContact',
      component: modifyContact
    }
  ]
})
