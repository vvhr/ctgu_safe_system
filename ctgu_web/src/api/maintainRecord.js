import request from '../utils/request'
export function getMaintainRecords(params) {
  return request({
    url: '/maintain-record/index',
    method: 'get',
    params: params
  })
}

export function createMaintainRecord(data) {
  return request({
    url: '/maintain-record/create',
    method: 'post',
    data: data
  })
}

export function updateMaintainRecord(data) {
  return request({
    url: '/maintain-record/update',
    method: 'post',
    data: data
  })
}

export function getMaintainRecord(params) {
  return request({
    url: '/maintain-record/view',
    method: 'get',
    params: params
  })
}

export function deleteUploadImgMaintainRecord(data) {
  return request({
    url: '/maintain-record/delete',
    method: 'post',
    data: data
  })
}
