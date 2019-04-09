import request from '../utils/request'
export function getDeviceParameterSettings(params) {
  return request({
    url: '/device-setting/index',
    method: 'get',
    params: params
  })
}

export function getDeviceParameterSetting(params) {
  return request({
    url: '/device-setting/view',
    method: 'get',
    params: params
  })
}

export function sendUpdateInstruction(params) {
  return request({
    url: '/device-setting/send-update-instruction',
    method: 'post',
    data: params
  })
}
