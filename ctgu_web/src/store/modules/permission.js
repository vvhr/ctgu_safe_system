import { asyncRouterMap, constantRouterMap } from '@/router'

/**
 * 通过meta.role判断是否与当前用户权限匹配。路由权限判断
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.indexOf(role) >= 0)
  } else {
    return true
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param roles
 */
// 以异步路由映射与角色为参数，获取角色过滤后的可访问路由映射
function filterAsyncRouter(asyncRouterMap, roles) {
  // 对异步路由表进行递归筛选
  const accessedRouters = asyncRouterMap.filter(route => {
    // 角色无对应权限则返回false并过滤掉。
    if (hasPermission(roles, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, roles)
      }
      return true
    }
    return false
  })
  return accessedRouters
}

const permission = {
  state: {
    // 固定路由
    routers: constantRouterMap,
    // 新增路由
    addRouters: []
  },
  // 路由同步异变:将新增路由添加到固定由上。
  mutations: {
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers
      state.routers = constantRouterMap.concat(routers)
    }
  },
  // 路由异步异变
  actions: {
    GenerateRoutes({ commit }, data) {
      return new Promise(resolve => {
        const { roles } = data
        let accessedRouters
        // 如果角色是admin，则将所有异步路由添加到固定路由
        if (roles.indexOf('admin') >= 0) {
          accessedRouters = asyncRouterMap
        } else {
          // 否则，使用filterAsyncRouter筛选出角色对应的路由
          accessedRouters = filterAsyncRouter(asyncRouterMap, roles)
        }
        console.log('经过路由异步异变后，最终可访问路由：accessedRouters：', accessedRouters)
        commit('SET_ROUTERS', accessedRouters)
        resolve()
      }).catch(error => {
        console.log(error)
      })
    }
  }
}

export default permission
