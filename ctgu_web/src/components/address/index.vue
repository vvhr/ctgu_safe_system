<template>
  <!--高德地址选择控件-->
  <el-cascader
    change-on-select
    style="width: 300px"
    :options="districts"
    @change="handleDistrictChange"
    :props="props"
  ></el-cascader>
</template>

<script>
import { loadAMap } from '../../utils/AMap'

export default {
  name: 'addressComponent',
  // 设置组件属性。'value'是必设属性
  props: {
    // 属性名必须是vlue以使v-model能搭配使用，再加watch行为，实现v-model自动反射
    // 总结：只要子组件拥有value属性，就可以实现v-model自动反射
    value: {
      type: Array,
      required: false,
      default() {
        return []
      }
    }
  },
  watch: {
    // v-model绑定的值改变，连动触发该方法改变其它值
    value(v) {
      // this.selectedValue = v
      // $nextTick指加入视图更新队列,表示当值改变后，主动触发的一些视图操作。这个回调会在值改变后值行，尤其是当值改变是异步的时候。
      // 这里的值改变指的是观察属性value的改变
      this.$nextTick(() => {
        // ..
        // console.log('当值改变时的自动反射：当前选择的region_id数组', regionIdArr)
        // 使用值改变当前地址组件选项视图
      })
    }
  },
  computed: {
    districts: function() {
      return this.$store.state.address.districts
    }
  },
  data() {
    return {
      center: null,
      MapTool: null,
      MapSearcher: null,
      // selectedValue: [],
      // 区域级联菜单指定作为value与children的字段
      props: {
        value: 'name',
        label: 'name',
        children: 'children'
      }
    }
  },
  mounted() {
    this.initMapTool()
  },
  methods: {
    /**
     * 1、加载地图jssdk，并实例化高德地图区域搜索工具
     * 2、搜索省级列表并初始化到this.districts中去
     * @returns {Promise<any | never>}
     */
    initMapTool() {
      // eslint-disable-next-line
      // loadAMap().then()
      loadAMap().then(_AMap => {
        this.MapTool = _AMap
        // 创建区域搜索器实例
        const opts = {
          // 只获取下一级子区域列表
          subdistrict: 1,
          // 街道为最终搜索级别
          showbiz: false
        }
        // 实例化区域搜索器
        // eslint-disable-next-line
        this.MapSearcher = new AMap.DistrictSearch(opts);
        console.log('initMapTool：this.MapSearcher已创建')
        if (this.districts.length === 0) {
          let districtFromLocalStorage
          // localStorage.removeItem('hj_safe_platform_web_districts')
          if (window.localStorage && (districtFromLocalStorage = localStorage.getItem('hj_safe_platform_web_districts'))) {
            this.$store.commit('Set_Districts', JSON.parse(districtFromLocalStorage))
            console.log('initMapTool:从本地存储读取hj_safe_platform_web_districts，并使用它初始化全局地址状态')
          } else {
            this.MapSearcher.search('中国', (status, result) => {
              if (status === 'complete') {
                // tempDistrictList是一个数组，由多少子区域信息组成
                const provinceList = result.districtList[0].districtList
                provinceList.map(v => {
                  v.children = []
                })
                // 异变地址大数组主状态
                this.$store.commit('Set_Districts', provinceList)
                // 如果浏览器支持localStorage: 每次将更新的地址大数组字符串化后存入本地持久化存储
                if (window.localStorage) {
                  localStorage.setItem('hj_safe_platform_web_districts', JSON.stringify(provinceList))
                }
                console.log('initMapTool：中国 的子区域列表填充并异变到主状态完毕')
                console.log('initMapTool：从远程高德api接口初始化全局地址状态完成')
              }
            })
          }
        }
      })
    },
    // 获取下级行政区域数据的通用方法，toObj是指加入到哪个行政区域的子列表
    /**
     * obj:被搜查的行政区域对象
     * 返回子区域列表
     * */
    fetchSubDistrictsToObj(obj, mainObj, needChildren = true) {
      console.log('-------------->fetchSubDistrictsToObj开始执行')
      console.log('fetchSubDistrictsToObj：开始试图获取 ' + '' + ' 的子区域列表')
      const timer = new Date()
      // const thisApp = this
      if (obj.children.length > 0) {
        // 如果传入的对象children不为空，则停止追加子区域列表
        console.log('<--------------fetchSubDistrictsToObj 子区域列表已存在，结束执行')
      } else {
        this.MapSearcher.search(obj.adcode, (status, result) => {
          if (status === 'complete') {
            // console.log(districtName + ' 的子区域列表获取完毕,result.districtList[0]', result.districtList[0])
            const districtData = result.districtList[0]
            // 这种写法是错的，使得toObj不再指向this.districts
            // toObj = districtData.districtList
            // 如果有区域列表
            if (districtData.districtList !== undefined) {
              // tempDistrictList是一个数组，由多少子区域信息组成
              const tempDistrictList = districtData.districtList
              if (needChildren) {
                tempDistrictList.map(v => {
                  // thisApp.$set(v, 'children', [])
                  v.children = []
                })
              }
              const timer2 = new Date()
              tempDistrictList.map(v => {
                obj.children.push(v)
              })
              // 异变地址大数组主状态
              this.$store.commit('Set_Districts', mainObj)
              // 如果浏览器支持localStorage: 每次将更新的地址大数组字符串化后存入本地持久化存储
              if (window.localStorage) {
                localStorage.setItem('hj_safe_platform_web_districts', JSON.stringify(mainObj))
              }
              console.log(obj.name + ' 的子区域列表填充并异变到主状态完毕,共花费时间(毫秒)：', timer2 - timer)
              console.log('<--------------fetchSubDistrictsToObj：结束执行')
              // 如果没有区域列表
            } else {
              console.log(obj.name + ' 没有子区域列表')
              console.log('<--------------fetchSubDistrictsToObj结束执行')
            }
          }
        })
      }
    },
    /* ---------------------子级行政区域搜索相关方法--------------------- */
    // 当级联菜单改变时，动态加载子区域, v是当前点击的选项的各级value组成的数组，数组的长度可以判断现在点的是哪一级
    // 当点选级联菜单时：
    //   触发动作1：改变value的值
    //   触发动作2：获取该点选地址的子区域列表（在获取函数中，会检测子区域列表是否为空，不为空的话不会获取，也不会执任何获取的代码）
    handleDistrictChange(v) {
      // 主动触发input事件处理器，使得v-model绑定的值发生改变！！！
      this.$emit('input', v)
      // 将地址数组获取到临时对象，最后用于异变
      // this.districts是一个数组
      const tempDistricts = this.districts.concat([])
      const addressLevel = v.length
      let obj_province = null
      let obj_city = null
      let obj_district = null
      // let, const, function and class声明语句不允许在case子句中出现！！！
      // 不管何时点击地址，都要得到所点的省的对象。任何点选事件都一定存在v[0]
      obj_province = this.queryDistrictObj(v[0], tempDistricts)
      switch (addressLevel) {
        // 添加城市列表到省
        case 1:
          this.fetchSubDistrictsToObj(obj_province, tempDistricts)
          this.center = obj_province.center
          break
        // 添加区县到城市
        case 2:
          obj_city = this.queryDistrictObj(v[1], obj_province.children)
          // 如果是获取三级地址列表，表示二级地址列表已存在，不用再获取
          this.fetchSubDistrictsToObj(obj_city, tempDistricts)
          this.center = obj_city.center
          break
        // 添加街道乡镇到区县
        case 3:
          obj_city = this.queryDistrictObj(v[1], obj_province.children)
          obj_district = this.queryDistrictObj(v[2], obj_city.children)
          // 如果是获取三级地址列表，表示二级地址列表已存在，不用再获取
          this.fetchSubDistrictsToObj(obj_district, tempDistricts, false)
          this.center = obj_district.center
          break
        default:
          break
      }
    },
    // 查询行政区域在districts中的子对象并返回
    queryDistrictObj(districtName, districts) {
      for (const i in districts) {
        if (districts[i].name === districtName) {
          return districts[i]
        }
      }
      return {}
    }
  }
}
</script>
