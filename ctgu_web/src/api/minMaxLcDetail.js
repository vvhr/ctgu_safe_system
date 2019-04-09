import request from '../utils/request'

export function createMinMaxLcDetailByDate(params) {
  return request({
    url: '/min-max-lc-detail/create-min-max-lc-detail-by-date',
    method: 'post',
    data: params,
    timeout: 180000
  })
}

export function getMinMaxDetailLcs(params) {
  return request({
    url: '/min-max-lc-detail/index',
    method: 'get',
    params: params
  })
}
