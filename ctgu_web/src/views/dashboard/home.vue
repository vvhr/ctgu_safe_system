<template>
  <div class="home-container">
    <!--背景-->
    <div class="home-container-bg">

      <!--头部 标题 时间 按钮-->
      <div class="row-head" align="center">
        <!--时钟-->
        <div class="time" style="width: 300px">
          <el-button class="timer" size="mini" type="text" icon="el-icon-time">时间 : {{nowTime}}</el-button>
        </div>
        <div class="info">
          <i class="el-icon-edit" style="color: #0dfdf3;margin-right: 10px"></i>
          <my-marquee :lists="info"></my-marquee>
          <!--<el-button class="name" size="mini" type="text">尊敬的用户: admin , 华建用电安全监控平台欢迎您!</el-button>-->
        </div>
        <!--标题-->
        <div class="imgTitle" align="center"></div>
        <!--按钮-->
        <div class="exitButton">
          <el-button class="setting" style="width: 150px" size="mini" type="primary" icon="el-icon-d-caret" @click="fullScreenChange">切换全屏(F11)</el-button>
          <el-button class="setting" size="mini" type="primary" icon="el-icon-setting" @click="onClickSetting">设置</el-button>
          <el-button class="exit" size="mini" type="primary" icon="el-icon-close" @click="logOut">退出</el-button>
        </div>
      </div>

      <!--边框-->
      <div class="home-container-border">
        <div class="row-01">
          <!--左栏-->
          <div class="left-label">
            <!--当前区域-->
            <div class="left-label-son-01">
              <div class="label-son-title">
                <div class="label-son-title-text">{{ default_address | get_last_two_district_in_a_json_address }}</div>
                <el-button @click="visible.placeChange = true" type="success" class="place-change-btn" size="mini" icon="el-icon-edit">修改区域</el-button>
              </div>
            </div>
            <!--设备总数-->
            <div class="left-label-son-02">
              <div class="label-son-title">
                <div class="label-son-title-text" style="font-size: 30px">{{totalCount.device}}</div>
                <el-button type="primary" class="place-change-btn" size="mini" @click="visible.devicesManage = true" icon="el-icon-menu">设备管理</el-button>
              </div>
            </div>
            <!--监控用户-->
            <div class="left-label-son-05">
              <div class="label-son-title">
                <div class="label-son-title-text" style="font-size: 30px">{{totalCount.runDevices}}</div>
                <el-button type="primary" class="place-change-btn" size="mini" @click="onClickShowDevices" icon="el-icon-time">实时状态</el-button>
              </div>
            </div>
            <!--按钮组-->
            <div class="button-group">
              <el-row style="text-align: center;padding: 40px 5px 5px;">
                <el-button style="width: 100px;height: 70px" :disabled="true">
                  <i class="el-icon-warning" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">报警记录</a>
                </el-button>
                <el-button style="width: 100px;height: 70px" :disabled="true">
                  <i class="el-icon-mobile-phone" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">短信记录</a>
                </el-button>
                <el-button style="width: 100px;height: 70px" :disabled="true">
                  <i class="el-icon-question" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">漏电分析</a>
                </el-button>
              </el-row>
              <el-row style="padding: 5px;text-align: center">
                <el-button style="width: 100px;height: 70px" @click="visible.user = true">
                  <i class="el-icon-star-on" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">用户管理</a>
                </el-button>
                <el-button style="width: 100px;height: 70px" @click="visible.project = true">
                  <i class="el-icon-menu" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">项目管理</a>
                </el-button>
                <el-button style="width: 100px;height: 70px" :disabled="true">
                  <i class="el-icon-share" style="font-size: 30px;margin-bottom: 3px"></i><br />
                  <a class="" style="font-weight: bold">异常维护  </a>
                </el-button>
              </el-row>
            </div>
          </div>
          <!--左栏 end-->
          <!--中部-->
          <div class="center-label">
            <el-tooltip placement="top">
              <div slot="content">
                当前主题: {{mapStyles[settingForm.defaultMapStyleId].name}}<br/>
                主题ID: {{settingForm.defaultMapStyleId}}
              </div>
              <el-button class="exit" style="float: right;position: relative;top: -35px;right: 10px;margin-right: 20px" size="mini" type="primary" icon="el-icon-refresh" @click="onClickChangeMapStyle">切换地图主题</el-button>
            </el-tooltip>
            <el-tooltip placement="top">
              <div slot="content" style="justify-content: center;">
                当前样式: {{markStyles[settingForm.defaultMarkStyleId].name}}<br/>
                样式ID: {{settingForm.defaultMarkStyleId}}
                <el-row>
                  <el-col :span="10">正常: </el-col>
                  <el-col :span="4"><div :class="'_marker_' + this.markStyles[settingForm.defaultMarkStyleId].value + '_0'"></div></el-col>
                </el-row>
                <el-row></el-row>
                <el-row></el-row>
                <el-row>
                  <el-col :span="10">报警: </el-col>
                  <el-col :span="4"><div :class="'_marker_' + this.markStyles[settingForm.defaultMarkStyleId].value + '_1'"></div></el-col>
                </el-row>
                <el-row>
                  <el-col :span="10">故障: </el-col>
                  <el-col :span="4"><div :class="'_marker_' + this.markStyles[settingForm.defaultMarkStyleId].value + '_2'"></div></el-col>
                </el-row>
              </div>
              <el-button class="exit" style="float: right;position: relative;top: -35px;margin-right: 20px" size="mini" type="primary" icon="el-icon-refresh" @click="onClickChangeMarkStyle">切换设备样式</el-button>
            </el-tooltip>
            <div class="app-container-row-03">
              <div class="left">
                <div class="body" id="home-map-01"></div>
                <div class="map-side-left"></div>
                <div class="map-side-right"></div>
              </div>
              <!--点击标记物弹出包含与设备相关的菜单的对话框 暂时无用，可去掉-->
              <div id="map-device-info" v-show="visible.mapDeviceInfo" style="background: #666;color: white;" @contextmenu.prevent="infoWindow.close()">
                <table class="map-popover">
                  <tr>
                    <th>设备信息</th>
                    <td>
                      UUID ：{{activeDevice.uuid}}<br>
                      安装地址：{{activeDevice.district + activeDevice.township + activeDevice.address}}<br>
                      设备状态：{{states[activeDevice.state]}}<br>
                    </td>
                  </tr>
                  <!--设备实时运行参数-->
                  <tbody v-if="activeDevice.report !== undefined">
                  <tr style="height: 50px">
                    <th>运行参数</th>
                    <td>
                      电压值：{{activeDevice.report.v/100}} (V)
                      <a style="left: 160px;position: absolute">电流值：{{activeDevice.report.c/1000}} (A)</a><br>
                      漏电流：{{activeDevice.report.lc}} (mA)
                      <a style="left: 160px;position: absolute">温度值：{{activeDevice.report.t/10}} (C)</a><br>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!--中部 end-->
          <!--右栏-->
          <div class="right-label">
            <!--违章提醒-->
            <div class="right-label-son-03">
              <div class="label-son-title">
                <div class="label-son-title-text" style="font-size: 30px" :style="numberColorChange(totalCount.illegal)">{{totalCount.illegal}}</div>
                <el-button type="primary" class="label-son-btn" size="mini" @click="onClickShowDevices(2)" icon="el-icon-bell">查看违章</el-button>
              </div>
            </div>
            <!--实时报警-->
            <div class="right-label-son-10">
              <div class="label-son-title">
                <div class="label-son-title-text" style="font-size: 30px" :style="numberColorChange(totalCount.alarms)">{{totalCount.alarms}}</div>
                <el-button type="danger" class="label-son-btn" size="mini" @click="onClickShowDevices(3)" icon="el-icon-search">查看报警</el-button>
              </div>
            </div>
            <!--实时故障-->
            <div class="right-label-son-09">
              <div class="label-son-title">
                <div class="label-son-title-text" style="font-size: 30px" :style="numberColorChange(totalCount.unWork)">{{totalCount.unWork}}</div>
                <el-button type="warning" class="label-son-btn" size="mini" @click="onClickShowDevices(1)" icon="el-icon-search">查看故障</el-button>
              </div>
            </div>
            <!--街道详情-->
            <div class="right-label-son-xiaqu">
              <div class="xiaqu-list scroll-bar">
                <ul>
                  <li v-for="(v,k) in deviceExceptionTotalByDistrict" :key="k">
                    <div style="width: 120px;background: #0251a1;color: #01f8fb;font-weight: bold;">{{k}}</div>
                    <div style="width: 110px;background: rgb(4, 67, 132);color: rgb(0, 243, 255)">监控 {{v.deviceCount}} 户</div>
                    <div style="width: 110px;background: #043567;color:rgb(255, 212, 0)">月报警 {{v.exceptionCount}} 次</div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!--右栏 end-->
        </div>
      </div>
      <!--所有对话框-->
      <div>
        <!--对话框 修改当前区域-->
        <div class="placeChange">
          <el-dialog title="修改当前区域" :visible.sync="visible.placeChange" :lock-scroll="true">
            <div class="placeChange-content">
              <myAddress :message="parentMsg" v-on:listenToChildEvent="showMsgFromPlaceChange"></myAddress>
            </div>
          </el-dialog>
        </div>
        <!--对话框 实时状态-->
        <div class="showDevices" v-show="vShow.devicesInfo">
          <el-dialog title="监控用户及实时状态" :visible.sync="visible.devicesInfo" :lock-scroll="true">
            <div class="devicesInfo-content">
              <devicesInfo ref="devicesInfo"></devicesInfo>
            </div>
          </el-dialog>
        </div>
        <!--对话框 设备管理-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="设备管理" :visible.sync="visible.devicesManage" :lock-scroll="true">
            <div class="devicesInfo-content">
              <deviceManage></deviceManage>
            </div>
          </el-dialog>
        </div>
        <!--对话框 运行电器-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="运行电器" :visible.sync="visible.runApps" :lock-scroll="true">
            <div class="devicesInfo-content">
              <appliance></appliance>
            </div>
          </el-dialog>
        </div>
        <!--对话框 高位电器-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="运行中的高危电器" :visible.sync="visible.riskApp" :lock-scroll="true">
            <div class="devicesInfo-content">
              <riskApp></riskApp>
            </div>
          </el-dialog>
        </div>
        <!--对话框 报警记录-->
        <div class="showDevices showDevicesManage" v-show="vShow.alarms">
          <el-dialog title="历史报警记录" :visible.sync="visible.alarms" :lock-scroll="true">
            <div class="devicesInfo-content">
              <alarms ref="alarms"></alarms>
            </div>
          </el-dialog>
        </div>
        <!--对话框 短信记录-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="短信发送记录" :visible.sync="visible.sendMassage" :lock-scroll="true">
            <div class="devicesInfo-content">
              <sendMassage></sendMassage>
            </div>
          </el-dialog>
        </div>
        <!--对话框 实时异常-->
        <div class="showDevices showDevicesManage" v-show="vShow.alarmManage">
          <el-dialog title="实时异常" :visible.sync="visible.alarmManage" :lock-scroll="true">
            <div class="devicesInfo-content">
              <alarmsManage ref="alarmsManage"></alarmsManage>
            </div>
          </el-dialog>
        </div>
        <!--对话框 漏电分析-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="漏电分析" :visible.sync="visible.event_record" :lock-scroll="true">
            <div class="devicesInfo-content">
              <event_record></event_record>
            </div>
          </el-dialog>
        </div>
        <!--对话框 异常维护-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="设备异常维护" :visible.sync="visible.alarmHandel" :lock-scroll="true">
            <div class="devicesInfo-content">
              <alarmHandel></alarmHandel>
            </div>
          </el-dialog>
        </div>
        <!--对话框 用户管理-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="用户管理" :visible.sync="visible.user" :lock-scroll="true">
            <div class="devicesInfo-content">
              <user></user>
            </div>
          </el-dialog>
        </div>
        <!--对话框 项目管理-->
        <div class="showDevices showDevicesManage">
          <el-dialog title="项目管理" :visible.sync="visible.project" :lock-scroll="true">
            <div class="devicesInfo-content">
              <project></project>
            </div>
          </el-dialog>
        </div>
        <!--对话框 设置页面-->
        <div class="placeChange">
          <el-dialog title="系统设置" :visible.sync="visible.setting" :lock-scroll="true">
            <div class="placeChange-content">
              <el-form label-width="150px" :model="settingForm">
                <el-form-item label="实时监控速率(ms)">
                  <el-input size="small" style="width: 200px" type="number" v-model.number="settingForm.refreshSpeed"></el-input>
                </el-form-item>
                <el-form-item label="默认地图主题(id)">
                  <el-input size="small" style="width: 200px" type="number" v-model.number="settingForm.defaultMapStyleId"></el-input>
                </el-form-item>
                <el-form-item label="默认设备样式(id)">
                  <el-input size="small" style="width: 200px" type="number" v-model.number="settingForm.defaultMarkStyleId"></el-input>
                </el-form-item>
                <el-form-item label="操作">
                  <el-button size="small" type="primary" @click="savedSetting">保存并应用</el-button>
                </el-form-item>
              </el-form>
            </div>
          </el-dialog>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import { Loading } from 'element-ui'
  import { loadAMap, selectedAddressToParams } from '../../utils/AMap'
  import { getDevices, getDeviceTotalGroupByDistrict } from '../../api/device'
  import { getDevicesTotalCount } from '../../api/deviceReportNew'
  import { getDeviceExceptionTotal, getTotalGroupByDistrict, getTotalGroupByMonthOfYear } from '../../api/exceptionReportNew'
  import { parseTime } from '../../utils'
  // import { getSmsList } from '../../api/smsLog'
  import devicesInfo from '../monitor/newInfo'
  import myMarquee from './components/myMarquee'
  import alarms from '../history/alarm'
  import alarmsManage from '../monitor/alarm'
  // import myAddress from '../setting/address'
  // import deviceManage from '../pdManage/device'
  // import appliance from '../monitor/appliance'
  // import riskApp from '../monitor/riskApp'
  // import sendMassage from '../history/sms-log'
  // import leakage_event from '../history/leakage_event'
  // import user from '../pdManage/user'
  // import project from '../pdManage/project'
  // import alarmHandel from '../maintain/alarm'
  import { getDeviceReportOne } from '../../api/deviceReportNew'

export default {
    components: {
      myAddress: resolve => { require(['../setting/address'], resolve) },
      myMarquee,
      devicesInfo,
      deviceManage: resolve => { require(['../pdManage/device'], resolve) },
      appliance: resolve => { require(['../monitor/appliance'], resolve) },
      riskApp: resolve => { require(['../monitor/riskApp'], resolve) },
      alarms,
      alarmHandel: resolve => { require(['../maintain/alarm'], resolve) },
      alarmsManage,
      sendMassage: resolve => { require(['../history/sms-log'], resolve) },
      event_record: resolve => { require(['../history/event_record'], resolve) },
      user: resolve => { require(['../pdManage/user'], resolve) },
      project: resolve => { require(['../pdManage/project'], resolve) }
    },
    data() {
      return {
        AMap: null,
        map: null,
        totalCount: {
          device: 0,
          runDevices: 0,
          alarms: 0,
          unWork: 0,
          illegal: 0
        },
        states: {
          '0': '正常',
          '1': '报警',
          '2': '故障'
        },
        settingForm: {
          refreshSpeed: 3000,
          defaultMapStyleId: 0,
          defaultMarkStyleId: 0
        },
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
        exceptionTotalGroupByMonthOfYear: [],
        searchForm: { enable: 1, pageSize: 10000 },
        mapOption: {
          buildingAnimation: true, // 楼块出现是否带动画
          zoom: 17,
          center: [111.321678, 30.723026],
          layers: [],
          viewMode: '3D',
          mapStyle: 'amap://styles/light'
        },
        // 新UI
        nowTime: '获取时间中 ...',
        loadingInstance: null, // 全局加载动画
        info: [
          '尊敬的用户 : ' + this.$store.getters.name + ' 您好! 欢迎使用三峡大学智慧用电安全监控平台!本平台将实时为您提供所有监控设备的最新状况! '
        ],
        timer: null,
        activeDevice: {},
        visible: {
          placeChange: false,
          devicesInfo: true,
          mapDeviceInfo: false,
          devicesManage: false,
          alarms: true,
          sendMassage: false,
          event_record: false,
          alarmManage: true,
          alarmHandel: false,
          user: false,
          project: false,
          setting: false
        },
        vShow: {
          alarms: false,
          alarmManage: false,
          devicesInfo: false
        },
        mapStyles: [
          { id: 0, mapStyle: 'amap://styles/light', name: '月光银' },
          { id: 1, mapStyle: 'amap://styles/normal', name: '标准' },
          { id: 2, mapStyle: 'amap://styles/dark', name: '幻影黑' },
          { id: 3, mapStyle: 'amap://styles/whitesmoke', name: '远山黛' },
          { id: 4, mapStyle: 'amap://styles/fresh', name: '草色青' },
          { id: 5, mapStyle: 'amap://styles/grey', name: '雅士灰' },
          { id: 6, mapStyle: 'amap://styles/macaron', name: '马卡龙' },
          { id: 7, mapStyle: 'amap://styles/blue', name: '靛青蓝' },
          { id: 8, mapStyle: 'amap://styles/darkblue', name: '极夜蓝' }
        ],
        markStyles: [
          { id: 0, value: '1', name: '圆点' },
          { id: 1, value: '2', name: '图标' }
        ],
        // 子组件参数
        parentMsg: 'hello',
        // 全屏
        fullScreen: false,
        // 刷新间隔时间
        refreshSpeed: 10000,
        ifCanSetTimeout: true,
        timer_: null
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
        this.loadingInstance = Loading.service({ fullscreen: true, background: 'rgba(37, 37, 37, 0.8)', text: '正在加载系统....(若等待时间超过10秒请按F5刷新!)' })
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
        // 获取按月统计并创建图表
        this.fetchTotalGroupByMonthOfYear().then(res => {
          this.createChart01()
        })
        // 获取设备异常总数
        this.fetchDeviceExceptionTotal()
        // 将设备列表转为Map映射:
        // 第一步：得到一个区域-》异常总数映射列表
        this.fetchDeviceExceptionGroupByDistrict(districtLevelForGroupBy).then(res => {
          console.log('fetchDeviceExceptionGroupByDistrict res>>', res)
          const exceptionMap = res
          // 第二步：得到区域-》设备总数映射列表
          getDeviceTotalGroupByDistrict(Object.assign({}, selectedAddressToParams(JSON.parse(this.default_address)), { district_level: districtLevelForGroupBy })).then(deviceTotalGroup => {
            console.log('getDeviceTotalGroupByDistrict res >>', deviceTotalGroup)
            // 把两个映射合并
            this.deviceExceptionTotalByDistrict = this.mapDeviceListByDistrictWithTotal(deviceTotalGroup, exceptionMap, districtLevelForGroupBy)
          })
        })
        // 创建地图，并获取设备，布署地图
        this.createMap().then(res => {
          this.fetchDevices()
        })
      },
      // 点击标记物弹出信息窗体
      onClickMarker(device) {
        this.activeDevice = Object.assign({}, device)
        console.log('标记物被点击')
        getDeviceReportOne({ uuid: device.uuid }).then(res => {
          if (res) {
            this.$set(this.activeDevice, 'report', res)
            // console.log('this.activeDevice.info', res)
          }
        })
        const content = document.getElementById('map-device-info')
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
        this.visible.mapDeviceInfo = true
      },
      createMap() {
        return loadAMap().then(AMap => {
          this.AMap = AMap
          this.map = new this.AMap.Map('home-map-01', this.mapOption)
        })
      },
      // 刷新设备数据
      RefreshDevices() {
        return getDevices(this.finalSearchForm).then(data => {
          this.list = data._items
          // 从device表获取设备总数
          this.totalCount.device = data._meta.totalCount
          const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
          // 从device_report_new获取实时运行设备总数
          getDevicesTotalCount(Object.assign({}, addressParams)).then(res => {
            this.totalCount.runDevices = res
          })
          // 从device_report_new获取故障总数
          getDevicesTotalCount(Object.assign({}, addressParams, { unWork: 1 })).then(res => {
            this.totalCount.unWork = res
          })
          // 从device_report_new获取违章电器总数
          getDevicesTotalCount(Object.assign({}, addressParams, { illegal: 3000 })).then(res => {
            this.totalCount.illegal = res
          })
          // 从device_report_new获取报警总数
          getDevicesTotalCount(Object.assign({}, addressParams, { alarm: 1, lc: 10, t: 80 })).then(res => {
            this.totalCount.alarms = res
          })
        })
      },
      // 获取设备列表带关联地理坐标
      fetchDevices() {
        return getDevices(this.finalSearchForm).then(data => {
          // console.log('this.finalSearchForm>>', this.finalSearchForm)
          this.map.clearMap()
          this.list = data._items
          // 从device表获取设备总数
          this.totalCount.device = data._meta.totalCount
          const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
          // 从device_report_new获取实时运行设备总数
          getDevicesTotalCount(Object.assign({}, addressParams)).then(res => {
            this.totalCount.runDevices = res
          })
          // 从device_report_new获取故障总数
          getDevicesTotalCount(Object.assign({}, addressParams, { unWork: 1 })).then(res => {
            this.totalCount.unWork = res
          })
          // 从device_report_new获取报警总数
          getDevicesTotalCount(Object.assign({}, addressParams, { alarm: 1, lc: 30, t: 80 })).then(res => {
            this.totalCount.alarms = res
          })
          // 从device_report_new获取违章电器总数
          getDevicesTotalCount(Object.assign({}, addressParams, { illegal: 3000 })).then(res => {
            this.totalCount.illegal = res
          })
          this.loading.total = false
          // 地图标记物不能自动根据状态反应变化，必须手动添加
          this.addMarkerByList(this.list)
          if (this.list.length <= 0) {
            this.$message({ message: '该区域没有查询到设备', type: 'success' })
          }
          this.visible.alarms = false
          this.visible.alarmManage = false
          this.visible.devicesInfo = false
          this.$nextTick(() => { // 以服务的方式调用的 Loading 需要异步关闭
            this.loadingInstance.close()
            this.vShow.alarms = true
            this.vShow.alarmManage = true
            this.vShow.devicesInfo = true
          })
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
        return getTotalGroupByMonthOfYear(Object.assign(selectedAddressToParams(JSON.parse(this.default_address)), { year: date.getFullYear() })).then(res => {
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
            this.addMarker(item, '_marker_' + this.markStyles[this.settingForm.defaultMarkStyleId].value + '_' + item.state, tempPosition, zIndex)
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
          content: '<div class="' + markerClass + '">' + '</div>',
          zIndex: zIndex,
          offset: new this.AMap.Pixel(-13, -30)
        })
        marker.on('click', (event) => {
          this.onClickMarker(device)
        })
        this.map.add(marker)
      },
      /** style */
      numberColorChange(number) {
        if (Number(number) > 0) {
          return 'color: yellow'
        } else return 'color: #0dfdf3'
      },
      /** 登出 */
      logOut() {
        this.$store.dispatch('LogOut').then(() => {
          location.reload() // 为了重新实例化vue-router对象 避免bug
        })
      },
      /** 时钟 */
      nowTimes() {
        this.nowTime = parseTime(new Date(), '{y}-{m}-{d} {h}:{i}:{s}')
      },
      /** 样式切换 */
      onClickChangeMapStyle() {
        if (this.settingForm.defaultMapStyleId === this.mapStyles.length - 1) {
          this.settingForm.defaultMapStyleId = 0
        } else this.settingForm.defaultMapStyleId = this.settingForm.defaultMapStyleId + 1
        this.mapOption.styleUrl = this.mapStyles[this.settingForm.defaultMapStyleId].mapStyle
        console.log('this.mapOption.styleUrl', this.mapOption.styleUrl)
        console.log('this.defaultMapStyleId', this.settingForm.defaultMapStyleId)
        this.init()
      },
      onClickChangeMarkStyle() {
        if (this.settingForm.defaultMarkStyleId === this.markStyles.length - 1) {
          this.settingForm.defaultMarkStyleId = 0
        } else this.settingForm.defaultMarkStyleId = this.settingForm.defaultMarkStyleId + 1
        this.init()
      },
      onClickSetting() {
        console.log('我的设置 =>', this.settingForm)
        const mySetting = JSON.parse(localStorage.getItem('defaultSetting'))
        if (mySetting !== null) {
          if (mySetting.refreshSpeed === null) this.settingForm.refreshSpeed = 3000
          else this.settingForm.refreshSpeed = mySetting.refreshSpeed
          if (mySetting.defaultMarkStyleId === null) this.settingForm.defaultMarkStyleId = 0
          else this.settingForm.defaultMarkStyleId = mySetting.defaultMarkStyleId
          if (mySetting.defaultMapStyleId === null) this.settingForm.defaultMapStyleId = 0
          else this.settingForm.defaultMapStyleId = mySetting.defaultMapStyleId
        }
        this.visible.setting = true
      },
      // 保存设置
      savedSetting() {
        const mySetting = {
          refreshSpeed: this.settingForm.refreshSpeed,
          defaultMarkStyleId: Number(this.settingForm.defaultMarkStyleId),
          defaultMapStyleId: Number(this.settingForm.defaultMapStyleId)
        }
        localStorage.setItem('defaultSetting', JSON.stringify(mySetting))
        console.log('缓存请求 =>', JSON.stringify(mySetting))
        this.$message({ message: '设置已保存,需要刷新页面后生效', type: 'success' })
      },
      // 进入全屏
      enterFullScreen() {
        const de = document.documentElement
        if (de.requestFullscreen) {
          de.requestFullscreen()
        } else if (de.mozRequestFullScreen) {
          de.mozRequestFullScreen()
        } else if (de.webkitRequestFullScreen) {
          de.webkitRequestFullScreen()
        }
      },
      // 退出全屏
      exitFullScreen() {
        const de = document
        if (de.exitFullscreen) {
          de.exitFullscreen()
        } else if (de.mozCancelFullScreen) {
          de.mozCancelFullScreen()
        } else if (de.webkitCancelFullScreen) {
          de.webkitCancelFullScreen()
        }
      },
      fullScreenChange() {
        this.fullScreen = !this.fullScreen
        if (this.fullScreen === true) this.enterFullScreen()
        if (this.fullScreen === false) this.exitFullScreen()
      },
      /** 点击事件 */
      onClickShowDevices(params) {
        // 1断线 2违章 3报警
        this.$refs.devicesInfo.setFetchReportType(params)
        this.visible.devicesInfo = true
      },
      showMsgFromPlaceChange(data) {
        if (data === 'isChange') {
          this.init()
        }
      },
      /** 实时刷新 */
      // 轮循
      refresh() {
        this.RefreshDevices().then(data => {
          console.log('刷新')
          if (this.ifCanSetTimeout === true) {
            this.timer_ = setTimeout(this.refresh, this.refreshSpeed)
          }
          return true
        })
      },
      /** 读取缓存 */
      getLocalStorage() {
        const mySetting = JSON.parse(localStorage.getItem('defaultSetting'))
        if (mySetting !== null) {
          if (mySetting.refreshSpeed === null) this.refreshSpeed = 3000
          else this.refreshSpeed = mySetting.refreshSpeed
          if (mySetting.defaultMarkStyleId === null) this.settingForm.defaultMapStyleId = 0
          else this.settingForm.defaultMapStyleId = mySetting.defaultMarkStyleId
          if (mySetting.defaultMapStyleId === null) this.settingForm.defaultMapStyleId = 0
          else this.settingForm.defaultMapStyleId = mySetting.defaultMapStyleId
        }
        console.log('读取缓存设置信息 => ', mySetting)
        return true
      }
    },
    mounted() {
      this.init()
      this.refresh()
      this.getLocalStorage()
      if (this.time !== null) clearInterval(this.timer)
      this.timer = setInterval(this.nowTimes, 1000)
      console.log(this.mapStyles)
    },
    destroyed() {
      console.clear()
    }
  }
</script>
<style scoped>
  /*div{border: dashed 1px grey;}*/
  /*.app-container{color: #666;}*/

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}

  .app-container-row-03 .left{width: 70%;padding-right: 30px;}
  .app-container-row-03 .left .title{margin-bottom: 15px;background: white;padding: 10px;display: flex;justify-content:space-between}
  .app-container-row-03 .left .body{height: 523px;width: 790px;top: -2px;left: 5px;background: #1b5093;}
  .app-container-row-03 .right{width: 30%;}
  .app-container-row-03 .right .title{margin-bottom: 15px;background: white;padding: 10px;line-height: 40px;display: flex;justify-content: space-between}
  .app-container-row-03 .right .body{height: 400px;background: white;padding: 10px;overflow:scroll}
  .app-container-row-03 .right li{line-height: 30px; list-style-type: none;display: flex;text-align: center;margin-bottom: 5px;border: solid 1px #f6f6f6}
  .app-container-row-03 .right ul{padding: 0;margin: 0}

  /* new_style */
  .app-container-row-03{display: flex;padding-bottom: 13px;;}
  .home-container {width:100%;margin: 0;}
  .home-container-bg {
    height:1080px;
    background:-webkit-linear-gradient(top,rgb(0, 30, 76),rgb(65, 166, 245));
    /*background:-moz-linear-gradient(top,#0076d1,#5db8ff);*/
    /*background:-o-linear-gradient(top,#0076d1,#5db8ff);*/
    /*background:-ms-linear-gradient(top,#0076d1,#5db8ff)*/
  }
  .home-container-border {
    padding: 100px 100px 100px 50px;
    height: 950px;
    width: 1850px;
    background: url('../../../static/image/border.png') no-repeat;
    background-size: contain;
    margin: auto;
    position: relative;
    top: 0; left: 35px; bottom: 0; right: 0;
  }
  .row-01 { width: 1700px; justify-content: space-around; display: inline-flex;}
  .label-son-title-text { font-size: 14px;color: #0dfdf3;font-weight: bold;width: 160px;text-align: center }
  .place-change-btn { width: 80px;height: 40px;padding-left: 5px;padding-right: 5px;left: 25px;position: relative;color: #fff;}
  .label-son-btn { width: 80px;height: 40px;padding-left: 5px;padding-right: 5px;left: 10px;position: relative;color: #fff;}

  /*左边*/
  .row-01 .left-label {width: 400px;position: relative;margin-bottom: 30px;}
  .row-01 .left-label .left-label-son-01 {margin-bottom: 10px;background: url('../../../static/image/label/01.png') no-repeat;background-size: contain;width: 420px;height: 70px;position: relative;}
  .row-01 .left-label .left-label-son-02 {margin-bottom: 10px;background: url('../../../static/image/label/02.png') no-repeat;background-size: contain;width: 420px;height: 70px;position: relative;}
  .row-01 .left-label .left-label-son-05 {margin-bottom: 10px;background: url('../../../static/image/label/shishi.png') no-repeat;background-size: contain;width: 420px;height: 70px;position: relative;}
  .row-01 .left-label .label-son-title { width: 400px; height: 40px;display: inline-flex;align-items:center;padding-left: 150px;padding-top: 25px}
  .row-01 .center-label {background: url('../../../static/image/label/border-map.png') no-repeat;background-size: contain;width: 800px;position: relative;padding-top: 45px;}
  .msg-table td {background: rgba(29, 102, 172, 0.62);border-bottom: solid thin #0dfdf3;line-height: 16px;text-align: left;border-left-width: 0px;padding: 3px;}
  .map-side-left {background: url('../../../static/image/label/map-side-left.png') no-repeat;background-size: cover;height: 323px;width: 20px;top: 130px;left: -2px;z-index: 1000;float: outside;position: absolute;}
  .map-side-right {background: url('../../../static/image/label/map-side-right.png') no-repeat;background-size: cover;height: 323px;width: 20px;top: 130px;left: 780px;z-index: 1000;float: outside;position: absolute;}
  .button-group {margin-left: 20px;margin-top: 20px;background: url('../../../static/image/label/caidan.png') no-repeat;background-size: contain;width: 390px;height: 210px;position: relative;}
  /*右边*/
  .row-01 .right-label {width: 400px;position: relative;}
  .row-01 .right-label .label-son-title { width: 400px; height: 40px;display: inline-flex;align-items:center;padding-left: 150px;padding-top: 25px}
  .row-01 .right-label .right-label-son-03 {margin-bottom: 10px;background: url('../../../static/image/label/weizhang.png') no-repeat;background-size: contain;width: 400px;height: 70px;position: relative;}
  .row-01 .right-label .right-label-son-09 {margin-bottom: 10px;background: url('../../../static/image/label/guzhang.png') no-repeat;background-size: contain;width: 400px;height: 70px;position: relative;}
  .row-01 .right-label .right-label-son-10 {margin-bottom: 10px;background: url('../../../static/image/label/baojing.png') no-repeat;background-size: contain;width: 400px;height: 70px;position: relative;}
  .row-01 .right-label .right-label-son-xiaqu {margin-bottom: 10px;background: url('../../../static/image/label/border-xiaqu-tm.png') no-repeat;background-size: cover;width: 400px;height: 450px;position: relative;}

  .row-02 .left-label {background: url('../../../static/image/border-foot.png') no-repeat;background-size: contain;width: 800px;height: 220px;position: relative;}
  .row-02 .right-label {background: url('../../../static/image/border-foot.png') no-repeat;background-size: contain;width: 700px;height: 220px;position: relative;}
  .xiaqu-list { margin-left: 4px;width: 98%;height: 400px;background: rgba(8, 83, 148, 0.4);padding: 10px;overflow-y: auto;overflow-x: hidden;position: relative;top: 45px}
  .xiaqu-list li{line-height: 30px; list-style-type: none;display: flex;text-align: center;margin-bottom: 5px;padding-top: 2px}
  .xiaqu-list ul{padding: 0;margin: 0}

  /*头部顶部栏*/
  .row-head { margin-top: 25px; display: inline-flex; width: 1920px; height: 60px; position:relative; }
  .row-head .imgTitle { background: url('../../../static/image/title02.png') center; width: 793px; height: 163px; -moz-background-size: 100% 100%; -o-background-size: 100% 100%;-webkit-background-size: 100% 100%;background-size: 100% 100%;margin: auto;position: absolute; top: 50px; left: 0; bottom: 0; right: 0;}
  .row-head .exitButton { z-index: 1000; display: inline-flex;height: 60px;margin: auto;position: absolute;top: 120px; bottom: 0; right: 100px;}

  .exitButton .setting {font-size: 16px;color: #00dcff;background-color: rgba(64,158,255,0.2);border: 1px solid rgba(0, 179, 249,0.3);width: 100px;margin: 10px;}
  .exitButton .setting .el-button--primary:focus, .el-button--primary:hover {border-color: rgba(0, 179, 249,0.8);color: #fff;}
  .exitButton .exit {font-size: 16px;color: #00dcff;background-color: rgba(64,158,255,0.2);border: 1px solid rgba(0, 179, 249,0.3);width: 100px;margin: 10px}
  .exitButton .exit .el-button--primary:focus, .el-button--primary:hover {border-color: rgba(0, 179, 249,0.8);color: #fff;}

  .row-head .time {display: inline-flex;height: 40px;margin: auto;position: absolute;top: 0; bottom: 0; left: 50px;}
  .time .timer {margin-left: 50px;margin-top: 40px;width: 250px;font-size: 16px;color: #00dcff;}

  .row-head .info {display: inline-flex;height: 20px;margin: auto;position: absolute;top: 150px; bottom: 0; left: 360px; right: 0;text-align: center}
  .row-head .info .name {font-size: 16px;color: #00dcff;}

  /*重定义滚动条*/
  .scroll-bar::-webkit-scrollbar {width: 10px;height: 1px;}
  .scroll-bar::-webkit-scrollbar-thumb {/*滚动条里面小方块*/border-radius: 15px;-webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);background: #44d7ff;}
  .scroll-bar::-webkit-scrollbar-track {/*滚动条里面轨道*/-webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);border-radius: 15px;background: #ffffff;}

  .map-popover {
    font-size: 12px;
    background: #0dfdf3;
    color: #3A3A3A;
  }
  .map-popover td{width: 270px;border-bottom: solid thin #f0f0f0;line-height:15px;padding:1px 1px;text-align: left }
  .map-popover th{
    width: 30px;
    line-height:15px;
    padding:1px 1px;
    background: #ffffff;
    color: #000000;
    text-align: left }
</style>
<style>
  /*重定义对话框*/
  .placeChange .el-dialog {opacity: 0.9;width: 1000px;height: 650px;background: url('../../../static/image/label/dialog.png') no-repeat;background-size: contain;position: relative;margin: 0 auto 50px;-webkit-box-sizing: border-box;box-sizing: border-box;}
  /*关闭按钮*/
  .placeChange .el-icon-close:before {background-color: #44d7ff;color: #06163a;font-size: 25px; /* border-color: red; */ /* border: 1px; */content: "\E60F";top: 27px;position: relative;}
  /*标题框*/
  .placeChange .el-dialog__header {padding: 25px 20px 20px 120px;color: red;}
  /*标题*/
  .placeChange .el-dialog__title {line-height: 24px;font-size: 16px;font-weight: bold;color: #00ffff;font-style: italic;font-family: "Microsoft YaHei",cursive;}
  /*内容框*/
  .placeChange .el-dialog__body {height: 520px;margin: 10px 25px 30px 30px;padding: 10px;color: #606266;line-height: 24px;font-size: 14px;background: #e5f2ff;border-radius: 5px;border: 2px solid #46d9ff;}

  .showDevices .el-dialog {
    opacity: 0.9;width: 1300px;height: 850px;background: url('../../../static/image/label/dialog.png') no-repeat;background-size: contain;position: relative;margin: 0 auto 50px;-webkit-box-sizing: border-box;box-sizing: border-box;}
  /*关闭按钮*/
  .showDevices .el-icon-close:before {background-color: #44d7ff;color: #06163a;font-size: 25px; /* border-color: red; */ /* border: 1px; */content: "\E60F";top: 30px;right: 20px;position: relative;}
  /*标题框*/
  .showDevices .el-dialog__header {padding: 25px 20px 20px 150px;color: red;}
  /*标题*/
  .showDevices .el-dialog__title {line-height: 24px;font-size: 16px;font-weight: bold;color: #00ffff;font-style: italic;font-family: "Microsoft YaHei",cursive;}
  /*内容框*/
  .showDevices .el-dialog__body {
    padding: 5px 5px;
    width: 1250px;height: 700px;margin: 10px 25px 30px 30px;
    color: #606266;line-height: 24px;font-size: 14px;background: #e5f2ff;border-radius: 5px;border: 2px solid #46d9ff;}

  .showDevicesManage .el-dialog__header {padding: 25px 20px 20px 190px;color: red;}

  .button-group .el-button {
    display: inline-block;
    line-height: 1;
    white-space: nowrap;
    cursor: pointer;
    background: #02509f;
    border: 1px solid #0dfdf3;
    color: #fff;
    -webkit-appearance: none;
    text-align: center;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    outline: 0;
    margin: 0;
    -webkit-transition: .1s;
    transition: .1s;
    /* padding: 12px 20px; */
    font-size: 14px;
    border-radius: 4px;
  }

  .button-group .el-button:focus, .el-button:hover {
    color: #ffffff;
    border-color: #00c7c7;
    background-color: #00c7c7;
  }

  .el-dialog__wrapper {
     overflow: hidden;
  }

  /*重定义地图组件的弹出框*/
  .amap-info-content {
    padding: 1px 1px 1px 1px;
    background: #0dfdf3;
    border: 1px solid #0dfde2;
  }
</style>
