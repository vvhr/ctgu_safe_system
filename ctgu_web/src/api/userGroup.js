import request from '../utils/request'

export function getUserGroups(params) {
  return request({
    url: '/user-group/index',
    method: 'get',
    params: params
  })
}
