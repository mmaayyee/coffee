import Vue from 'vue'
import Router from 'vue-router'
import pointRecord from '@/components/page/pointRecord'
import pointDetail from '@/components/page/pointDetail'
import pointList from '@/components/page/pointList'
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
      path: '/point',
      name: 'pointRecord',
      component: pointRecord
    },
    {
      path: '/detail',
      name: 'pointDetail',
      component: pointDetail
    },
    {
      path: '/list',
      name: 'pointList',
      component: pointList
    },
    {
      path: '/contact',
      name: 'modifyContact',
      component: modifyContact
    }
  ]
})
