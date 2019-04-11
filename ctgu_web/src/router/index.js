import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../views/layout/Layout'

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
**/
// 固定路由映射：login 404 dashboard/dashboard
export const constantRouterMap = [
  { path: '/login', component: () => import('../views/login/index'), hidden: true },
  { path: '/404', component: () => import('../views/404'), hidden: true },

  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    name: 'dashboard',
    hidden: true,
    children: [{
      path: 'dashboard',
      component: () => import('../views/dashboard/home')
    }]
  }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

// 异步路由映射
export const asyncRouterMap = [
  {
    path: '/home',
    component: Layout,
    name: 'home',
    meta: { title: '面板', icon: 'user', roles: ['admin', 'personal', 'district'] },
    children: [
      {
        path: 'index',
        name: 'homeIndex',
        component: () => import('../views/dashboard/home'),
        meta: { title: '首页', icon: 'user', roles: ['admin', 'personal', 'district'] }
      },
      {
        path: 'bigScreen',
        name: 'homeBigScreen',
        hidden: true,
        component: () => import('../views/dashboard/bigScreen'),
        meta: { title: '大屏展示', icon: 'user', roles: ['admin', 'personal', 'district'] }
      }
    ]
  },
  {
    path: '/monitor',
    component: Layout,
    name: 'monitor',
    meta: { title: '实时监控', icon: 'check', roles: ['admin', 'personal', 'district'] },
    children: [
      {
        path: 'map',
        name: 'monitorMap',
        component: () => import('../views/monitor/map'),
        meta: { title: '地图监控', icon: 'process', roles: ['admin', 'personal', 'district'] }
      },
      {
        path: 'alarm',
        name: 'monitorAlarm',
        component: () => import('../views/monitor/alarm'),
        meta: { title: '实时异常', icon: 'process', roles: ['admin', 'personal', 'district'] }
      },
      {
        path: 'info',
        name: 'monitorInfo',
        component: () => import('../views/monitor/newInfo'),
        meta: { title: '实时状态', icon: 'process', roles: ['admin', 'personal', 'district'] }
      }
    ]
  },
  {
    path: '/pdManage',
    component: Layout,
    name: 'pdManage',
    meta: { title: '项目设备', icon: 'check', roles: ['admin'] },
    children: [
      {
        path: 'project',
        name: 'pdManageProject',
        component: () => import('../views/pdManage/project'),
        meta: { title: '项目管理', icon: 'process', roles: ['admin'] }
      },
      {
        path: 'device',
        name: 'pdManageDevice',
        component: () => import('../views/pdManage/device'),
        meta: { title: '设备管理', icon: 'process', roles: ['admin'] }
      },
      {
        path: 'user',
        name: 'pdManageUser',
        component: () => import('../views/pdManage/user'),
        meta: { title: '用户管理', icon: 'process', roles: ['admin'] }
      }
    ]
  },
  {
    path: '/setting',
    component: Layout,
    name: 'setting',
    meta: { title: '系统设置', icon: 'check', roles: ['admin'] },
    children: [
      {
        path: 'address',
        name: 'settingAddress',
        component: () => import('../views/setting/address'),
        meta: { title: '默认区域设置', icon: 'process', roles: ['admin'] }
      },
      {
        path: 'group',
        name: 'settingGroup',
        component: () => import('../views/setting/userGroup'),
        meta: { title: '用户组管理', icon: 'process', roles: ['admin'] }
      }
    ]
  },
  { path: '*', redirect: '/404', hidden: true }
]
