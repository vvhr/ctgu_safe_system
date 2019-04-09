// 全局状态获取器
const getters = {
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  alarm_type: state => state.app.alarm_type,
  alarm_cause: state => state.app.alarm_cause,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  userId: state => state.user.userId,
  default_address: state => state.user.default_address,
  roles: state => state.user.roles,
  permission_routers: state => state.permission.routers,
  addRouters: state => state.permission.addRouters,
  districts: state => state.address.districts,
  selectedDistrict: state => state.address.selectedAddress
}
export default getters
