import request from '../utils/request'

export function getSmsList(params = {}) {
  return request({
    url: 'sms-log/index',
    method: 'get',
    params: params
  })
}
