<template>
  <div class="app-container">
    <!--<div class="app-container-row-01">云监控数据概览</div>-->
    <div class="app-container-header">
    </div>
    <div class="app-container-row-01">
      <div class="app-container-row-02">
        <div class="app-container_row-02_title" >系统总览</div>
        <div class="summary-box">
          <div class="left box-shadow-deep" ><img width="50px" height="50px" :src="img_bigScreen_area" alt=""></div>
          <div class="right box-shadow-deep" style="font-size: 14px"><span>当前区域</span><span style="color: #1296db">{{default_address | get_last_two_district_in_a_json_address}}</span></div>
        </div>
        <div class="summary-box">
          <div class="left box-shadow-deep" ><img width="50px" height="50px" :src="img_bigScreen_device" alt=""></div>
          <div class="right box-shadow-deep" style="font-size: 14px" v-loading="loading.total"><span>设备总数</span><span style="color: #1afa29">{{totalCount.device}} 台</span></div>
        </div>
        <div class="summary-box">
          <div class="left box-shadow-deep" ><img width="50px" height="50px" :src="img_bigScreen_warn" alt=""></div>
          <div class="right box-shadow-deep" style="font-size: 14px" v-loading="loading.deviceExceptionTotal_month"><span>当月报警</span><span style="color: #ef3636">{{deviceExceptionTotal_month}} 台次</span></div>
        </div>
        <div class="summary-box">
          <div class="left box-shadow-deep" ><img width="50px" height="50px" :src="img_bigScreen_warn1" alt=""></div>
          <div class="right box-shadow-deep" style="font-size: 14px" v-loading="loading.deviceExceptionTotal_day"><span>当天报警</span><span style="color: #ff375b;">{{deviceExceptionTotal_day}} 台次</span></div>
        </div>
      </div>
      <div class="app-container-row-03">
        <div class="left">
          <div class="app-container_row-03_left_title" >深圳设备分布图</div>
          <div class="body box-shadow bigMap" style="width:99.8%;height:89%;border-radius: 10px" id="home-map-01"></div>
        </div>
        <div class="right">
          <div class="app-container_row-03_title" >区域统计</div>
          <div class="body box-shadow">
            <ul>
              <li v-for="(v,k) in deviceExceptionTotalByDistrict" :key="k">
                <div style="width: 120px;background: #61d2f7;color: white;text-align: center;line-height: 60px;">{{k}}用户数</div>
                <div style="width: 100px;background: #06152c;color: #1dbba5;text-align: center;line-height: 60px;">{{v.deviceCount}}户</div>
                <div style="width: 120px;background: #61d2f7;color:white;text-align: center;line-height: 60px;">当月报警次数</div>
                <div style="width: 80px;background: #06152c;color:#ff6633;text-align: center;line-height: 60px;">{{v.exceptionCount}} 次</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="app-container-footer">
      <div class="footer-left">
        <div class="footer-center-title">高危电器开关占比图(月)</div>
        <div id="riskAppPieChart" class="box-shadow-deep"></div>
      </div>
      <div class="footer-center">
        <div class="footer-center-title" >实时状态</div>
        <el-table :data="report_real_time_list" stripe size="mini"  class="_el-table">
          <el-table-column label="用户" width="250">
            <template slot-scope="scope">
              <div>
            <span v-if="scope.row.user">
              <i class="el-icon-news"> {{ scope.row.user.username }}</i>
              <i class="el-icon-phone-outline"> {{ scope.row.user.phone }}</i>
            </span>
                <span style="color: red" v-else>
              <i class="el-icon-news"> 未绑定用户</i>
            </span>
              </div>
              <div><i class="el-icon-info" title="设备uuid"> {{scope.row.uuid}}</i></div>
            </template>
          </el-table-column>
          <el-table-column label="电流(A) 电压(V) 漏电流(mA) 温度(C) 上报时间]" align="left">
            <template slot-scope="scope">
              <div v-if="scope.row.report">
                <el-button  type="text" style="width: 50px;"><i class="el-icon-info" style="color: #26a6f5">{{scope.row.report.electricity/1000}}</i></el-button>
                <el-button  type="text" style="width: 50px"><i class="el-icon-info" style="color: #26a6f5">{{scope.row.report.voltage/100}}</i></el-button>
                <el-button  type="text" style="width: 50px"><i class="el-icon-info" style="color: #26a6f5">{{scope.row.report.leakageCurrent}}</i></el-button>
                <el-button  type="text" style="width: 50px"><i class="el-icon-info" style="color: #26a6f5">{{scope.row.report.temperature/10}}</i></el-button>
                <el-button  type="text" style="width: 100px"><i class="el-icon-info" style="color: #26a6f5">[{{scope.row.report.reportTime}}]</i></el-button>
              </div>
              <div v-else style="text-align: center"> 暂无上传数据 </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="footer-right">
        <div class="footer-center-title">高危电器开关占比图(年)</div>
        <div id="appTypePieChart" class="box-shadow-deep"></div>
      </div>
    </div>
  </div>
</template>
<script>
import { loadAMap, selectedAddressToParams } from '../../utils/AMap'
import { getDevices, getDeviceTotalGroupByDistrict } from '../../api/device'
import { getDeviceExceptionTotal, getTotalGroupByDistrict, getTotalGroupByMonthOfYear } from '../../api/deviceExceptionNew'
import { parseTime } from '../../utils'
import img_big_screen_header from '@/assets/bigScreen/big_screen_header.png'
import img_bigScreen_area from '@/assets/bigScreen/bigScreen_area.png'
import img_bigScreen_warn from '@/assets/bigScreen/bigScreen_warn.png'
import img_bigScreen_warn1 from '@/assets/bigScreen/bigScreen_warn1.png'
import img_bigScreen_border from '@/assets/bigScreen/bigScreen_boder.png'
import img_bigScreen_device from '@/assets/bigScreen/bigScreen_device.png'
import { getChartInstance, getChartOptionOfPie } from '../../utils/charts'
import { getLastInfoOfPerImei } from '../../api/reportRealTimeOneObjTab2'
import { getDangerDeviceRunCount } from '../../api/analysisResults'

const defaultSearchForm = { expand: 'user' }
export default {
  data() {
    return {
      img_big_screen_header,
      img_bigScreen_area,
      img_bigScreen_warn,
      img_bigScreen_warn1,
      img_bigScreen_device,
      img_bigScreen_border,
      dangerDeviceOpenCounts: {}, /** 危险电器数组 */
      AMap: null,
      map: null,
      ifCanSetTimeOut: true,
      totalCount: {
        device: 0
      },
      chartInstance01: null,
      chartInstance02: null,
      list: [],
      listOfUserDevice: [],
      // 按所选行政区域的子区域分组统计设备数量：由获取的设备列表，通过本地js方法映射获取
      deviceExceptionTotalByDistrict: [],
      total: 0,
      loading: {
        deviceExceptionTotal_month: true,
        deviceExceptionTotal_day: true,
        total: true
      },
      // 当月报警总数：直接请求接口获取
      deviceExceptionTotal_month: 0,
      // 当天报警总数：直接请求接口获取
      deviceExceptionTotal_day: 0,
      exceptionTotalGroupByMonthOfYear: [],
      searchForm: { enable: 1, pageSize: 10000 },
      mapOption: {
        zoom: 9,
        center: [114.070719, 22.658496],
        layers: [],
        viewMode: '2D',
        mapStyle: 'amap://styles/blue'
      },
      timeOut: null,
      chart01: null,
      year: '2018',
      year_options: [
        {
          value: '2018',
          label: '2018年度'
        },
        {
          value: '2017',
          label: '2017年度'
        },
        {
          value: '2016',
          label: '2016年度'
        }
      ],
      timeType: 1,
      searchForm_report_real_time_list: Object.assign({}, defaultSearchForm),
      pageInfo: {
        page: 1,
        pageSize: 5,
        totalCount: 0
      },
      report_real_time_list: [],
      applianceRunRecordsCount: {} /** 高危电器开启次数 */
    }
  },
  computed: {
    default_address() {
      return this.$store.state.user.default_address
    },
    finalSearchForm() {
      let addressParams = {}
      if (this.default_address) {
        addressParams = selectedAddressToParams(JSON.parse(this.default_address))
      }
      return Object.assign({}, this.searchForm, addressParams)
    },
    report_real_time() {
      let addressParams = {}
      if (this.default_address) {
        addressParams = selectedAddressToParams(JSON.parse(this.default_address))
      }
      return Object.assign({}, this.searchForm_report_real_time_list, this.pageInfo, addressParams)
    },
    finalAddress() {
      let addressParams = {}
      if (this.default_address) {
        addressParams = selectedAddressToParams(JSON.parse(this.default_address))
      }
      return addressParams
    }
  },
  filters: {
    get_last_two_district_in_a_json_address(json_address) {
      const addArr = JSON.parse(json_address)
      return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
    }
  },
  methods: {
    init() {
      // 获取当前需要按其分组的区域级别
      let districtLevelForGroupBy = ''
      switch (JSON.parse(this.default_address).length) {
        case 1:
          districtLevelForGroupBy = 'city'
          break
        case 2:
          districtLevelForGroupBy = 'district'
          break
        case 3:
          districtLevelForGroupBy = 'township'
          break
        default:
          districtLevelForGroupBy = 'street'
          break
      }
      // 获取设备异常总数
      this.fetchDeviceExceptionTotal()
      // 将设备列表转为Map映射:
      // 第一步：得到一个区域-》异常总数映射列表
      this.fetchDeviceExceptionGroupByDistrict(districtLevelForGroupBy).then(res => {
        console.log('fetchDeviceExceptionGroupByDistrict res>>', res)
        const exceptionMap = res
        // 第二步：得到区域-》设备总数映射列表
        getDeviceTotalGroupByDistrict(Object.assign({}, this.finalAddress, { district_level: districtLevelForGroupBy })).then(deviceTotalGroup => {
          console.log('getDeviceTotalGroupByDistrict res >>', deviceTotalGroup)
          // 把两个映射合并
          this.deviceExceptionTotalByDistrict = this.mapDeviceListByDistrictWithTotal(deviceTotalGroup, exceptionMap, districtLevelForGroupBy)
        })
      })
      // 创建地图，并获取设备，布署地图
      this.createMap().then(res => {
        this.fetchDevices()
      })
      this.chartInstance01 = getChartInstance('riskAppPieChart')
      this.chartInstance02 = getChartInstance('appTypePieChart')
    },
    createMap() {
      return loadAMap().then(AMap => {
        this.AMap = AMap
        this.map = new this.AMap.Map('home-map-01', this.mapOption)
      })
    },
    // 获取设备列表带关联地理坐标
    fetchDevices() {
      return getDevices(this.finalSearchForm).then(data => {
        this.map.clearMap()
        this.list = data._items
        this.totalCount.device = data._meta.totalCount
        this.loading.total = false
        // 地图标记物不能自动根据状态反应变化，必须手动添加
        this.addMarkerByList(this.list)
        if (this.list.length <= 0) {
          this.$message({ message: '该区域没有查询到设备', type: 'success' })
        }
      })
    },
    /** 获取每台设备最新一条上报信息 */
    fetchReport() {
      return getLastInfoOfPerImei(this.report_real_time).then(data => {
        console.log('getLastInfoOfPerImei111111 >>', data)
        this.report_real_time_list = data._items
        this.pageInfo.totalCount = data._meta.totalCount
      })
    },
    /** 按区域获取高危电器开启次数 **/
    fetchDangerDeviceRunCount(params) {
      return getDangerDeviceRunCount(params).then(res => {
        this.applianceRunRecordsCount = res.map(function(obj) {
          const robj = {}
          robj['name'] = (obj.address === undefined ? '' : obj.address) + obj.name
          robj['value'] = obj.value
          return robj
        })
        return true
      })
    },
    // 获取exceptionMap
    fetchDeviceExceptionGroupByDistrict(districtLevelForGroupBy) {
      // 按区域分组得出每个区的报警总数
      const now = new Date()
      const date_from = new Date(now.setDate(1))
      const date_to = new Date(now.setMonth(now.getMonth() + 1))
      const timeRange = {
        date_from: date_from.getFullYear() + '-' + (date_from.getMonth() + 1) + '-' + '01',
        date_to: date_to.getFullYear() + '-' + (date_to.getMonth() + 1) + '-' + '01'
      }
      const searchParams = Object.assign(selectedAddressToParams(JSON.parse(this.default_address)), { district_level: districtLevelForGroupBy }, timeRange)
      console.log('searchParams>>', searchParams)
      return getTotalGroupByDistrict(searchParams).then(res => {
        // console.log('getTotalGroupByDistrict res >>', res)
        const exceptionMap = {}
        res.forEach((v, k) => {
          console.log('getTotalGroupByDistrict res forEach >>', v, k)
          exceptionMap[v[districtLevelForGroupBy]] = v.total
          // this.$set(exceptionMap, v[districtLevelForGroupBy], v.total)
        })
        return exceptionMap
      })
    },
    fetchDeviceExceptionTotal() {
      let now = new Date()
      // 统计当月的报警总数
      let date_from = new Date(now.setDate(1))
      let date_to = new Date(now.setMonth(now.getMonth() + 1))
      let timeRange = {
        date_from: parseTime(date_from, '{y}-{m}-{d}') + ' 00:00:01',
        date_to: parseTime(date_to, '{y}-{m}-{d}') + ' 00:00:01'
      }
      // console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>', timeRange)
      getDeviceExceptionTotal(Object.assign(timeRange, this.finalSearchForm)).then(res => {
        this.deviceExceptionTotal_month = res
        this.loading.deviceExceptionTotal_month = false
      })
      // 统计当天的报警总数
      now = new Date()
      date_from = new Date(now)
      date_to = new Date(now.setDate(now.getDate() + 1))
      timeRange = {
        date_from: parseTime(date_from, '{y}-{m}-{d}') + ' 00:00:01',
        date_to: parseTime(date_to, '{y}-{m}-{d}') + ' 00:00:01'
      }
      // console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>', timeRange)
      getDeviceExceptionTotal(Object.assign(timeRange, this.finalSearchForm)).then(res => {
        this.deviceExceptionTotal_day = res
        this.loading.deviceExceptionTotal_day = false
      })
    },
    fetchTotalGroupByMonthOfYear() {
      const date = new Date()
      return getTotalGroupByMonthOfYear(Object.assign(this.finalAddress, { year: date.getFullYear() })).then(res => {
        this.exceptionTotalGroupByMonthOfYear = res
      })
    },
    /** 基础方法 */
    mapDeviceListByDistrictWithTotal(deviceTotalGroup, exceptionMap, districtLevelForGroupBy) {
      const deviceExceptionMap = {}
      deviceTotalGroup.forEach((item, key) => {
        // districtNameForGroupBy：示例值：深圳市
        const districtName = item[districtLevelForGroupBy]
        // 如果地区名不为空
        if (districtName) {
          // 先声明子元素
          deviceExceptionMap[districtName] = { deviceCount: 0, exceptionCount: 0 }
          // 赋值
          deviceExceptionMap[districtName].deviceCount = item.total
          if (exceptionMap[districtName] !== undefined) {
            deviceExceptionMap[districtName].exceptionCount = exceptionMap[districtName]
          } else {
            deviceExceptionMap[districtName].exceptionCount = 0
          }
        }
      })
      console.log('mapDeviceListByDistrictWithTotal res >>', deviceExceptionMap)
      return deviceExceptionMap
    },
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
            zIndex = 100
          }
          this.addMarker(item, '_marker_' + item.state, tempPosition, zIndex)
          count++
        } else {
          console.log(item, '-----------该设备缺坐标')
        }
      })
      console.log('addMarkerByList: 一共添加了' + count + '个标记物')
    },
    addMarker(device, markerClass, position, zIndex = 100) {
      const marker = new this.AMap.Marker({
        position: position,
        content: '<div class="' + markerClass + '">' + 'R' + '</div>',
        zIndex: zIndex
      })
      this.map.add(marker)
    },
    /** 获取高危电器开关字数(月)*/
    drawPieChartMonth() {
      const searchParams = Object.assign({}, { time_type: this.timeType }, this.finalAddress)
      return this.fetchDangerDeviceRunCount(searchParams).then(res => {
        this.chartInstance01.setOption(getChartOptionOfPie('', '开关次数', this.applianceRunRecordsCount, '#1076ddf2', [0, 100], '次', '#fff0', '60%'), true)
      })
    },
    /** 获取高危电器开关字数(区域)*/
    drawPieChartRegion() {
      // 获取当前需要按其分组的区域级别
      let districtLevelForGroupBy = ''
      switch (JSON.parse(this.default_address).length) {
        case 1:
          districtLevelForGroupBy = 'city'
          break
        case 2:
          districtLevelForGroupBy = 'district'
          break
        case 3:
          districtLevelForGroupBy = 'township'
          break
        default:
          districtLevelForGroupBy = 'street'
          break
      }
      const searchParams = Object.assign({ default_address: districtLevelForGroupBy }, this.finalAddress, { time_type: this.timeType })
      this.fetchDangerDeviceRunCount(searchParams).then(res => {
        this.chartInstance02.setOption(getChartOptionOfPie('', '开关次数', this.applianceRunRecordsCount, '#1076ddf2', [0, 150], '次数', '#fff0', '60%'), true)
      })
    },
    starFetchReport() {
      this.fetchReport().then(res => {
        this.timeOut = setInterval(this.fetchReport, 5000)
      })
    },
    stopFetchReport() {
      clearInterval(this.timeOut)
    }
  },
  mounted() {
    this.init()
    this.drawPieChartMonth()
    this.drawPieChartRegion()
    this.starFetchReport()
  },
  destroyed() {
    this.stopFetchReport()
    console.clear()
  }
}
</script>
<style scoped>
  .title{font-weight: bolder}
  .box-shadow-deep{display: flex; flex-direction: column; }
  .app-container{width: 100%;min-height:1200px;padding: 0;height: 100%;background: url("/static/image/background.jpg") no-repeat;background-size: cover}
  .app-container-header{height: 120px;display: flex;width: 1800px; margin:0 auto;}
  .app-container-row-01{width: 1800px; margin:0 auto;display: flex; flex-direction:row;}
  .app-container-row-02{display: flex;flex-direction:column; width: 30%; border: 1px solid #5487c8;margin-right: 30px;border-radius: 10px;} /** 调整设备总览 */
  .app-container-row-03{display: flex;width: 75%;} /** 地图和辖区总览 */

  .app-container-row-02 .summary-box{width: 100%;min-width: 200px;padding: 15px 60px;display: flex;line-height: 30px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 30%;color: white;border-radius: 10px 0 0 10px; background: #006a6af2;padding:10px 0 10px 30px;}
  .app-container-row-02 .summary-box .right{width: 70%;background: #0d71af; color: white}
  .app-container_row-02_title{color: white;width:42%;height: 10%;position: relative;left: 33%;top: -5%;line-height: 284%;text-align: center;background: url('../../assets/bigScreen/bigScreen_boder.png') -30px -19px no-repeat;}

  .app-container_row-03_left_title{color: white;width:29%;height: 11%;position: relative;left: 37%;top: -5%;line-height: 284%;text-align: center;background: url('../../assets/bigScreen/bigScreen_boder.png') -30px -19px no-repeat;}
  .app-container_row-03_title{color: white;width:52%;height: 10%;position: relative;left: 28%;top: -5%;line-height: 284%;text-align: center;background: url('../../assets/bigScreen/bigScreen_boder.png') -30px -19px no-repeat;}
  .app-container-row-03 .left{width: 59%; border: 1px solid #5487c8;margin-right: 30px;margin-top:26px;position:relative;height: 94.5%;border-radius: 10px} /**调整地图边框 */
  .app-container-row-03 .left .title{margin-bottom: 15px;background: white;display: flex;justify-content:space-between}
  .app-container-row-03 .left .body{height: 475px;background: white;}
  .app-container-row-03 .right{width: 40%; padding:0 50px; border: 1px solid #5487c8;border-radius: 10px;background: transparent} /** 调整辖区总览 **/
  .app-container-row-03 .right .body{height: 390px;background: transparent;border-radius: 10px}
  .app-container-row-03 .right li{line-height: 30px; list-style-type: none;display: flex;text-align: center;border: solid 1px #f6f6f6;margin: 30px 0}
  .app-container-row-03 .right ul{padding: 0;margin: 0}

  .app-container-footer{display: flex;flex-direction:row; width: 1800px;margin:0 auto;}
  .app-container-footer .footer-left{width: 28%;height:400px;margin-top: 30px; border: 1px solid #5487c8;border-radius: 10px}
  .app-container-footer .footer-center{width: 40%;height:400px;margin-top: 30px; border: 1px solid #5487c8;margin-left: 2%;border-radius: 10px}
  .app-container-footer .footer-right{width: 28%;height:400px;margin-top: 30px; border: 1px solid #5487c8;margin-left: 2%;border-radius: 10px}
  #riskAppPieChart{height: 300px;width: 100%;margin-bottom: 30%}
  #appTypePieChart{height: 300px;width: 100%;margin-bottom: 30%}
  .footer-center .footer-center-title{ height:14%;text-align: center;line-height: 363%;width: 30%;position: relative;top: -30px;left: 37%;background: url('../../assets/bigScreen/bigScreen_boder.png') -29px -14px no-repeat;}
  .footer-center-title{ height:14%;text-align: center;line-height: 363%;width: 42%;position: relative;top: -30px;left: 30%;background: url('../../assets/bigScreen/bigScreen_boder.png') -29px -14px no-repeat;}
</style>
<style>
  .app-container-footer *{
    color:white;
    background-color: transparent !important;
  }
</style>
