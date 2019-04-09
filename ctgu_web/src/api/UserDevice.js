import request from '../utils/request'

export function getUserDevices(params) {
  return request({
    url: '/user-device/index',
    method: 'post',
    params: params
  })
}

export function createUserDevice(data) {
  return request({
    url: '/user-device/create',
    method: 'post',
    data: data
  })
}

export function deleteUserDevice(data) {
  return request({
    url: '/user-device/delete',
    method: 'post',
    data: data
  })
}
export function deleteUserDeviceByUserIdAndHomeId(data) {
  return request({
    url: '/user-device/delete-by-user-id-and-home-id',
    method: 'post',
    data: data
  })
}
export function createByUserIdAndHomeId(data) {
  return request({
    url: '/user-device/create-by-user-id-and-home-id',
    method: 'post',
    data: data
  })
}

export function clearWrongHomeId() {
  return request({
    url: '/user-device/clear-wrong-home-id',
    method: 'post'
  })
}
