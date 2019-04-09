import request from '../utils/request'

export function getUserGroupApi(params) {
  return request({
    url: '/user-group-api/index',
    method: 'get',
    params: params
  })
}

export function updateUserGroupApi(params) {
  return request({
    url: '/user-group-api/update',
    method: 'post',
    data: params
  })
}
