import request from '../utils/request'
export function getChannels(params) {
  return request({
    url: '/home-user/device-channels',
    method: 'get',
    params: params
  })
}

export function getChannel(params = {}) {
  return request({
    url: '/home-user/view',
    method: 'get',
    params: params
  })
}

export function updateChannel(data = {}) {
  return request({
    url: '/home-user/update',
    method: 'post',
    data: data
  })
}

export function getHomeUser(params = {}) {
  return request({
    url: '/home-user/index',
    method: 'post',
    params: params
  })
}

