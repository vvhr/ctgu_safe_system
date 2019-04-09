import request from '../utils/request'

export function getExceptionList(params = {}) {
  return request({
    url: '/exception-report-tab/index',
    method: 'get',
    params: params
  })
}

