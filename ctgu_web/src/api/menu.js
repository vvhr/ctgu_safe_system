import request from '../utils/request'

export function getMenus(params) {
  return request({
    url: '/menu/index',
    method: 'get',
    params: params
  })
}
