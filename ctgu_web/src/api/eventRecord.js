import request from '../utils/request'
export function getEventRecord(params) {
  return request({
    url: '/event-record/index',
    method: 'get',
    params: params
  })
}
