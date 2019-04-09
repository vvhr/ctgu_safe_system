import request from '../utils/request'
/* 获取报警时相关的电器设备 */
export function getLeakagecurrentEvent(params = {}) {
  return request({
    url: '/leakagecurrent-event/index',
    method: 'get',
    params: params
  })
}

