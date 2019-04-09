import request from '../utils/request'

export function getUsers(params) {
  return request({
    url: '/user/index',
    method: 'get',
    params: params
  })
}

export function getUnbindUsers(params) {
  return request({
    url: '/user/get-unbind-users',
    method: 'get',
    params: params
  })
}

export function getUser(params) {
  return request({
    url: '/user/view',
    method: 'get',
    params: params
  })
}

export function createUser(data) {
  return request({
    url: '/user/create',
    method: 'post',
    data: data
  })
}

export function updateUser(data) {
  return request({
    url: '/user/update',
    method: 'post',
    data: data
  })
}

