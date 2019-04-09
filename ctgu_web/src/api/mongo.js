import request from '../utils/request'

export function getDeviceReportNews(params) {
  return request({
    url: '/mongo/get-device-report-news',
    method: 'get',
    params: params
  })
}
