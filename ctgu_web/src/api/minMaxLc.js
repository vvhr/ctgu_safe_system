import request from '../utils/request'

export function createMinMaxLcByDate(params) {
  return request({
    url: '/min-max-lc/create-min-max-lc-by-date',
    method: 'post',
    data: params,
    timeout: 180000
  })
}

export function getMinMaxLcs(params) {
  return request({
    url: '/min-max-lc/index',
    method: 'get',
    params: params
  })
}
