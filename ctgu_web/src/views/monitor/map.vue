<template>
  <div class="app-container">
    <div class="app-container-row-02">
      <div class="summary-box">
        <div class="left box-shadow-deep" style="background: #ff6633">当前区域</div>
        <div class="right box-shadow-deep">{{default_address | get_last_two_district_in_a_json_address}}</div>
      </div>
      <div class="summary-box">
        <div class="left box-shadow-deep" style="background: #ff9900">设备总数</div>
        <div class="right box-shadow-deep">{{totalCount.device}} 台</div>
      </div>
      <div class="summary-box">
        <div class="left box-shadow-deep" style="background: #76c80d">故障总数</div>
        <div class="right box-shadow-deep">{{totalCount.unWork}} 台</div>
      </div>
      <div class="summary-box">
        <div class="left box-shadow-deep" style="background: #1dbba5">报警总数</div>
        <div class="right box-shadow-deep">{{totalCount.alarms}} 台</div>
      </div>
    </div>
    <!--地图控件-->
    <div id="map-container-monitor"></div>
    <!--点击标记物弹出包含与设备相关的菜单的对话框 暂时无用，可去掉-->
    <div id="device-info" v-show="visible.deviceInfo" style="background: #666;color: white;" @contextmenu.prevent="infoWindow.close()">
      <table class="_table_no_hover">
        <tr>
          <th style="width: 40%">设备信息</th>
          <td style="min-width: 300px">
            UUID ：{{activeDevice.uuid}}<br>
            安装地址：{{activeDevice.district + activeDevice.township + activeDevice.address}}<br>
            设备状态：{{states[activeDevice.state]}}<br>
          </td>
        </tr>
        <!--设备实时运行参数-->
        <tbody v-if="activeDevice.report !== undefined">
        <tr>
          <th style="width: 40%">运行值</th>
          <td>
            电压：{{activeDevice.report.voltage/100}} (V)<br>
            电流：{{activeDevice.report.electricity/1000}} (A)<br>
            漏电流：{{activeDevice.report.leakageCurrent}} (mA)<br>
            温度：{{activeDevice.report.temperature/10}} (C)<br>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import { loadAMap, selectedAddressToParams } from '../../utils/AMap'
  import { getDevices, getDevicesTotalCount } from '../../api/device'
  import { getDeviceReportOne } from '../../api/deviceReportNew'

  export default {
    data() {
      return {
        infoWindow: {},
        searchForm: {
          enable: 1,
          pageSize: 50
        },
        visible: {
          dialog_1: false,
          deviceInfo: false
        },
        activeDevice: {},
        AMap: {},
        map: {},
        lines: {},
        polygons: {},
        mapOption: {
          zoom: 13,
          center: [114.070719, 22.658496],
          layers: [],
          viewMode: '2D'
        },
        list: [],
        states: {
          '0': '正常',
          '1': '掉线',
          '2': '报警'
        },
        totalCount: {
          alarms: 0,
          unWork: 0,
          device: 0
        }
      }
    },
    filters: {
      get_last_two_district_in_a_json_address(json_address) {
        const addArr = JSON.parse(json_address)
        return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
      }
    },
    computed: {
      default_address() {
        return this.$store.state.user.default_address
      },
      finalSearchForm() {
        return Object.assign(
          {},
          selectedAddressToParams(JSON.parse(this.default_address)),
          this.searchForm
        )
      }
    },
    methods: {
      init() {
        this.createMap().then(res => {
          this.fetchTotal()
          this.fetchDevices()
          this.map.on('click', (ev) => {
            // console.log('event.target.lnglat >>', ev.lnglat)
          })
        })
      },
      /* -------------------- 业务数据强耦合类方法 -----------------------*/
      // device/getDevices接口获得的业务数据来添加地图标记物
      addMarkerByList(list) {
        let count = 0
        let hadSetCenter = false
        list.forEach(item => {
          if (item.lat !== null && item.lon !== null) {
            // 把第一个合法坐标设为地图中心
            if (!hadSetCenter) {
              this.map.setCenter([item.lon, item.lat])
              hadSetCenter = true
            }
            const tempPosition = [item.lon, item.lat]
            let zIndex = 100
            if (item.state === 2) {
              zIndex = 101
            } else if (item.state === 1) {
              zIndex = 90
            } else {
              zIndex = 80
            }
            this.addMarker(item, '_marker_' + item.state, tempPosition, zIndex)
            count++
          }
        })
        console.log('>> addMarkerByList: 一共添加了' + count + '个标记物')
      },
      /* ---------------------接口类方法--------------------- */
      // 获取设备列表带关联地理坐标
      fetchDevices() {
        getDevices(this.finalSearchForm).then(data => {
          console.log('this.finalSearchForm >>', this.finalSearchForm)
          this.totalCount.device = data._meta.totalCount
          this.map.clearMap()
          this.list = data._items
          this.totalCount.devices = data._meta.totalCount
          // console.log('this.data', data)
          // 地图标记物不能自动根据状态反应变化，必须手动添加
          this.addMarkerByList(this.list)
          if (this.list.length > 0) {
            this.$message({ message: '查询完成', type: 'success' })
            console.log('>> 在该区域查询到' + this.list.length + '个设备')
          } else {
            this.$message({ message: '该区域没有查询到终端通迅点', type: 'success' })
          }
        })
      },
      fetchTotal() {
        getDevicesTotalCount(Object.assign({}, this.finalSearchForm, { state: 1 })).then(res => {
          this.totalCount.alarms = res
        })
        getDevicesTotalCount(Object.assign({}, this.finalSearchForm, { state: 2 })).then(res => {
          this.totalCount.unWork = res
        })
      },
      /* ---------------------地图工具类方法--------------------- */
      // 地图初始化到vue子组件map
      createMap() {
        return loadAMap().then(_AMap => {
          this.AMap = _AMap
          // 创建地图实例
          this.map = new this.AMap.Map('map-container-monitor', this.mapOption)
        })
      },
      // position:[116.39, 39.9] index: vue子组件状态：markers集合的索引 markerClass:各色的样式，定义在sytle中
      addMarker(device, markerClass, position, zIndex = 100) {
        const marker = new this.AMap.Marker({
          position: position,
          content: '<div class="' + markerClass + '">' + 'R' + '</div>',
          zIndex: zIndex
        })
        marker.on('click', (event) => {
          this.onClickMarker(device)
        })
        this.map.add(marker)
      },
      // 点击标记物弹出信息窗体
      onClickMarker(device) {
        this.activeDevice = Object.assign({}, device)
        // console.log('this.activeDevice', device)
        getDeviceReportOne({ uuid: device.uuid }).then(res => {
          if (res) {
            this.$set(this.activeDevice, 'report', res)
            // console.log('this.activeDevice.info', res)
          }
        })
        const content = document.getElementById('device-info')
        // 创建 infoWindow 实例
        // eslint-disable-next-line
        this.infoWindow = new AMap.InfoWindow({
          isCustom: false,
          content: content,
          // eslint-disable-next-line
          offset: new AMap.Pixel(0, -30)
        })
        // 打开信息窗体
        this.infoWindow.open(this.map, [device.lon, device.lat])
        this.visible.deviceInfo = true
      }
    },
    mounted() {
      this.init()
    },
    destroyed() {
      console.clear()
    }
  }
</script>
<style scoped>
  #map-container-monitor {width:100%; height: 800px; border: solid 1px #f0f0f0}
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .app-container-row-02{display: flex}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}
</style>
