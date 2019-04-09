import request from '../utils/request'

export function createMinMaxTpDetailByDate(params) {
  return request({
    url: '/min-max-tp-detail/create-min-max-tp-detail-by-date',
    method: 'post',
    data: params,
    timeout: 180000
  })
}

export function getMinMaxDetailTps(params) {
  return request({
    url: '/min-max-tp-detail/index',
    method: 'get',
    params: params
  })
}
