import request from '../utils/request'

export function getDevices(params) {
  return request({
    url: '/device/index',
    method: 'get',
    params: params
  })
}

export function getDevice(params) {
  return request({
    url: '/device/view',
    method: 'get',
    params: params
  })
}

export function updateDevice(data) {
  return request({
    url: '/device/update',
    method: 'post',
    data: data
  })
}

export function enableDevice(data) {
  return request({
    url: '/device/enable-device',
    method: 'post',
    data: data
  })
}
// 从mongodb上报记录表中抽出唯一的设备列表，对比mysql中的设备列表，imei不存的设备将插入设备表，并在通道表中插入相应的通道。
export function bulkAddDevice(data) {
  return request({
    url: '/device/bulk-add-device',
    method: 'post',
    data: data
  })
}

export function getDevicesTotalCount(params) {
  return request({
    url: '/device/total',
    method: 'get',
    params: params
  })
}
export function getDeviceTotalGroupByDistrict(params) {
  return request({
    url: '/device/total-group-by-district',
    method: 'get',
    params: params
  })
}
export function bulkBindUser(data) {
  return request({
    url: '/device/bulk-bind-user',
    method: 'post',
    data: data
  })
}
export function bulkBindProject(data) {
  return request({
    url: '/device/bulk-bind-project',
    method: 'post',
    data: data
  })
}

export function updateChannel(data = {}) {
  return request({
    url: '/device/update',
    method: 'post',
    data: data
  })
}
