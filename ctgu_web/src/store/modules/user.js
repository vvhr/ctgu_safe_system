import { login, getInfo } from '../../api/login'
import { getToken, setToken, removeToken } from '../../utils/auth'
import { getSetting } from '../../api/UserSetting'
// 存储：核心行为
const user = {
  state: {
    token: getToken(),
    name: '',
    avatar: '',
    roles: [],
    userId: 1,
    default_address: ''
  },

  // 全局状态异变方法
  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_AVATAR: (state, avatar) => {
      state.avatar = avatar
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_USER_ID: (state, userId) => {
      state.userId = userId
    },
    SET_Default_Address: (state, data) => {
      state.default_address = data
    }
  },

  // 全局状态异步异变方法
  actions: {
    // 登录动作 被登录页面所dispatch
    Login({ commit }, userInfo) {
      const username = userInfo.username.trim()
      return new Promise((resolve, reject) => {
        login(username, userInfo.password).then(response => {
          const data = response.data
          setToken(data.token)
          commit('SET_TOKEN', data.token)
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },
    GetSetting({ commit }) {
      getSetting().then(res => {
        commit('SET_Default_Address', res ? res.default_address : null)
      })
    },
    // 获取用户信息动作，被permission入口所dispatch
    GetInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        getInfo(state.token).then(response => {
          const data = response.data
          if (data.groupInfo.role && data.groupInfo.role.length > 0) { // 验证返回的roles是否是一个非空数组
            commit('SET_ROLES', [data.groupInfo.role])
          } else {
            reject('getInfo: roles must be a non-null array !')
          }
          commit('SET_NAME', data.username)
          commit('SET_AVATAR', '')
          commit('SET_USER_ID', data.id)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    // LogOut({ commit, state }) {
    //   return new Promise((resolve, reject) => {
    //     logout(state.token).then(() => {
    //       commit('SET_TOKEN', '')
    //       commit('SET_ROLES', [])
    //       removeToken()
    //       resolve()
    //     }).catch(error => {
    //       reject(error)
    //     })
    //   })
    // },
    // 登出
    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        commit('SET_TOKEN', '')
        commit('SET_ROLES', [])
        removeToken()
        resolve()
        // logout(state.token).then(() => {
        //   commit('SET_TOKEN', '')
        //   commit('SET_ROLES', [])
        //   removeToken()
        //   resolve()
        // }).catch(error => {
        //   reject(error)
        // })
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        commit('SET_ROLES', [])
        removeToken()
        resolve()
      })
    }
  }
}

export default user
