import request from '../utils/request'

export function getProjects(params = {}) {
  return request({
    url: '/project/index',
    method: 'get',
    params: params
  })
}

export function getProject(params = {}) {
  return request({
    url: '/project/view',
    method: 'get',
    params: params
  })
}

export function createProject(data = {}) {
  return request({
    url: '/project/create',
    method: 'post',
    data: data
  })
}
export function updateProject(data = {}) {
  return request({
    url: '/project/update',
    method: 'post',
    data: data
  })
}
