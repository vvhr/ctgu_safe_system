import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '../store'
import { getToken } from '@/utils/auth'

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 15000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(config => {
  config.headers['Accept'] = 'application/json'
  // Accept: application/json
  if (store.getters.token) {
    // config.headers['token'] = getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
    config.headers['Authorization'] = 'Bearer ' + getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
  }
  return config
}, error => {
  // Do something with request error
  console.log(error) // for debug
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
  /**
  * code为非101是抛错 可结合自己业务进行修改
   * response.status = 401会直接进入error调用环节
  */
    // console.log('每次异步请求响应的数据', response)
    const res = response.data
    // 在请求成功200的情况下
    if (response.status === 200) {
      // 存在code 并且 code不等于101, 相当于操作失败，或操作无效时
      if (res.code !== undefined && res.code !== 101) {
        // 非101码要弹出框明示错误
        Message({
          message: res.msg,
          type: 'error',
          duration: 5 * 1000
        })

        // 0:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
        if (res.code === 401) {
          MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
            confirmButtonText: '重新登录',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            store.dispatch('FedLogOut').then(() => {
              location.reload()// 为了重新实例化vue-router对象 避免bug
            })
          })
        }
        return Promise.reject('error')
      } else {
        return response.data
      }
    } else {
      // 0:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
      if (response.status === 401) {
        MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
          confirmButtonText: '重新登录',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          store.dispatch('FedLogOut').then(() => {
            location.reload()// 为了重新实例化vue-router对象 避免bug
          })
        })
      }
      // 非200状态要弹出框明示错误
      Message({
        message: response.status + ':' + response.statusText,
        type: 'error',
        duration: 5 * 1000
      })
    }
  },
  error => {
    // console.log('err' + error)// for debug
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
