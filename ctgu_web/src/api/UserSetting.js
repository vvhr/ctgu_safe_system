import request from '../utils/request'

export function getSetting() {
  return request({
    url: '/user-setting/get',
    method: 'get'
  })
}
export function setSetting(params = {}) {
  return request({
    url: '/user-setting/set',
    method: 'post',
    data: params
  })
}

