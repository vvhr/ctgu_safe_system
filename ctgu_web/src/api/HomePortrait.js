import request from '../utils/request'
export function getHomePortraits(params = {}) {
  params.expand = 'device'
  return request({
    url: '/home-portrait/index',
    method: 'get',
    params: params
  })
}
export function getAppTotalCount(params) {
  return request({
    url: '/home-portrait/get-app-total-count',
    method: 'get',
    params: params
  })
}
