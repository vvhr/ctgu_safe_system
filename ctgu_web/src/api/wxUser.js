import request from '../utils/request'

export function enableOrNotReceiveMsg(data) {
  return request({
    url: '/wx-user/enable-or-not-receive-msg',
    method: 'post',
    data: data
  })
}
export function sendWxAlarmMessage(data) {
  return request({
    url: '/wx-user/send-alarm-message',
    method: 'post',
    data: data
  })
}
