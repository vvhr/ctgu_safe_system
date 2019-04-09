export function _join(params, split = '&') {
  let _ = ''
  for (const k in params) {
    _ = _ + k + '=' + params[k] + split
  }
  _ = _.substr(0, _.length - 1)
  return _
}

export function _join2(params, split = ',') {
  let _ = ''
  for (const k in params) {
    _ = _ + k + ':' + params[k] + split
  }
  _ = _.substr(0, _.length - 1)
  return _
}

export function _join3(params, split = ',') {
  let _ = ''
  for (const k in params) {
    _ = _ + params[k] + split
  }
  _ = _.substr(0, _.length - 1)
  return _
}
