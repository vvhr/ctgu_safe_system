import request from '../utils/request'
export function getDeviceReports(params) {
  return request({
    url: '/device-report-new/index',
    method: 'get',
    params: params
  })
}

export function getDeviceReport(params = {}) {
  return request({
    url: '/device-report-new/view',
    method: 'get',
    params: params
  })
}
// actionCloseOverPowerAppliance
export function closeOverPowerAppliance(params = {}) {
  return request({
    url: '/device-report-new/cpa',
    method: 'get',
    params: params
  })
}

export function getDevicesTotalCount(params) {
  return request({
    url: '/device-report-new/total',
    method: 'get',
    params: params
  })
}

export function getDeviceReportOne(params = {}) {
  return request({
    url: '/device-report-new/view',
    method: 'get',
    params: params
  })
}
