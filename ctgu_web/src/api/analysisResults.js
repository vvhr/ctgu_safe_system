import request from '../utils/request'

export function getApplianceRunRecords(params = {}) {
  return request({
    url: '/analysis-results/index',
    method: 'get',
    params: params
  })
}
export function getAppRunStatusesByHomeId(params = {}) {
  return request({
    url: '/analysis-results/app-run-statuses-by-home-id',
    method: 'get',
    params: params
  })
}

/**
 * @deprecated 该方法已停用
 * @param params
 */
export function getRunAppTotalCount(params = {}) {
  return request({
    url: '/analysis-results/get-run-app-total-count',
    method: 'get',
    params: params
  })
}

/**
 * 获取高危电器运行次数
 * @param params
 */
export function getDangerDeviceRunCount(params = {}) {
  return request({
    url: '/analysis-results/get-danger-device-run-count',
    method: 'get',
    params: params
  })
}
