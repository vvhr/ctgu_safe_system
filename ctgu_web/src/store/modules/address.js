// 存储：核心行为 存储已异步获取的地址级联菜单数据到大数组
const address = {
  state: {
    // 当前的地址级联菜单大数组
    districts: [],
    // 当前选中的地址（是一个数组，如[省名，市名，区名]）
    selectedDistrict: []
  },
  // 全局状态异变方法
  mutations: {
    Set_Districts: (state, districts) => {
      state.districts = districts
    },
    Set_SelectedDistrict: (state, selectedDistrict) => {
      state.selectedDistrict = selectedDistrict
    }
  }
}
export default address
