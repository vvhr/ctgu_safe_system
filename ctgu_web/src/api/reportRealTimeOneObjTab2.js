import request from '../utils/request'

export function getLastInfoOfPerImei(params = {}) {
  return request({
    url: '/report-real-time-one-obj-tab2/index',
    method: 'get',
    params: params
  })
}

export function getDeviceReportOne(params = {}) {
  return request({
    url: '/report-real-time-one-obj-tab2/view',
    method: 'get',
    params: params
  })
}
// actionCloseOverPowerAppliance
export function closeOverPowerAppliance(params = {}) {
  return request({
    url: '/report-real-time-one-obj-tab2/cpa',
    method: 'get',
    params: params
  })
}

