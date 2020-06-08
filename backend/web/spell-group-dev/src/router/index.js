import Vue from 'vue'
import Router from 'vue-router'
import addSpellGoods from '@/components/page/addSpellGoods'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/add'
    },
    {
      path: '/add',
      name: 'addSpellGoods',
      component: addSpellGoods
    }
  ]
})
