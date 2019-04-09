import request from '../utils/request'
export function getDeviceExceptions(params) {
  return request({
    url: '/device-exception-new/index',
    method: 'get',
    params: params
  })
}

export function getDeviceExceptionTotal(params) {
  return request({
    url: '/device-exception-new/total',
    method: 'get',
    params: params
  })
}
export function getTotalGroupByDistrict(params) {
  return request({
    url: '/device-exception-new/total-group-by-district',
    method: 'get',
    params: params
  })
}
export function getTotalGroupByMonthOfYear(params) {
  return request({
    url: '/device-exception-new/total-group-by-month-of-year',
    method: 'get',
    params: params
  })
}

export function clearAlarm(data) {
  return request({
    url: '/device-exception-new/clear-alarm',
    method: 'post',
    data: data
  })
}

