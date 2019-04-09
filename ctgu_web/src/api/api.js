import request from '../utils/request'

export function getApiList(params) {
  return request({
    url: '/api/index',
    method: 'get',
    params: params
  })
}

export function bulkAddApi(params) {
  return request({
    url: '/api/bulk-add-api',
    method: 'get',
    params: params
  })
}
