<template>
  <div class="app-container-info">
    <div class="app-container-row-03">
      <!--分页-->
      <div>
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page="parseInt(pageInfo.page)"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="5"
          layout="total, sizes, prev, pager, next, jumper"
          :total="parseInt(pageInfo.totalCount)">
        </el-pagination>
      </div>
      <!--筛选表单-->
      <div>
        <el-form :inline="true" :model="searchForm">
          <el-form-item label="设备uuid">
            <el-input v-model="searchForm.uuid" placeholder="请输入设备uuid编号"></el-input>
          </el-form-item>
          <el-form-item label="用户">
            <el-select
              v-model="searchForm.user_id"
              filterable
              remote
              reserve-keyword
              placeholder="请输入关键词"
              :remote-method="getRemoteUsers"
              :loading="usersLoading">
              <el-option
                v-for="user in remoteUsers"
                :key="user.id"
                :label="user.username"
                :value="user.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button-group>
              <el-button type="primary" @click="onClickSearchBtn">查询</el-button>
              <el-button type="primary" @click="resetSearchForm">重置</el-button>
            </el-button-group>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <!--表格区-->
    <el-table height="600" :data="deviceReports" stripe border :header-cell-style="{textAlign: 'center'}">
      <el-table-column label="宿舍" width="260">
        <template slot-scope="scope">
          <div>
            <span v-if="scope.row.user">
              <i class="el-icon-news"> {{ scope.row.user.username }}</i>
              <i class="el-icon-phone-outline"> {{ scope.row.user.phone }}</i>
            </span>
            <span style="color: red" v-else>
              <i class="el-icon-news"> 未绑定用户</i>
            </span>
            <i class="el-icon-location"> {{ scope.row.device.address }} </i>
          </div>
          <div><i class="el-icon-info" title="设备uuid"> {{scope.row.uuid}}</i></div>
        </template>
      </el-table-column>
      <el-table-column label="地址" min-width="220">
        <template slot-scope="scope">
          <i class="el-icon-location-outline" v-if="scope.row.device.lat">
            {{ scope.row.device.city }}
            {{ scope.row.device.district }}
            {{ scope.row.device.township }}
            {{ scope.row.device.street }}
            {{ scope.row.device.address }}
          </i>
          <i class="el-icon-location-outline" v-else> 地址未设置</i>
        </template>
      </el-table-column>
      <el-table-column label="实时监控" min-width="80" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="showHomeApp(scope.row)">查看</el-button>
        </template>
      </el-table-column>
      <el-table-column label=" 漏电流(mA) 温度(C) | 电压(V) 电流(A)  有功(W)" width="450" align="center">
        <template slot-scope="scope">
          <div :style="scope.row.eType===0?{color: '#409EFF'}:{color: 'orangered'}" v-if="scope.row" @click="onClickChangeChartData(scope.$index)" title="点击可将本通道显示为波形图">
            <i class="el-icon-info" style="width: 80px"> {{scope.row.lc}}mA</i>
            <i class="el-icon-info" style="width: 80px"> {{scope.row.t/10}}℃</i> |
            <i class="el-icon-info" style="width: 80px"> {{scope.row.v/100}}V</i>
            <i class="el-icon-info" style="width: 80px"> {{scope.row.c/100}}A</i>
            <i class="el-icon-info" style="width: 80px"> {{scope.row.p}}W</i>
          </div>
          <div v-else> 暂无上传数据 </div>
        </template>
      </el-table-column>
      <el-table-column label="状态" min-width="60" align="center">
        <template slot-scope="scope">
          <!--<span style="color: red" v-if="scope.row.eType === 1">报警</span>-->
          <!--<span style="color: orange" v-else-if="scope.row.eType === 2">故障</span>-->
          <!--<span style="color: #409EFF" v-else>正常</span>-->
          <span style="color: #409EFF" v-if="new Date(scope.row.reportTime).valueOf() + heartbeat_Time < new Date().valueOf()">  设备已断线 </span>
          <span style="color: #409EFF" v-else-if="scope.row.eType === 0">正常</span>
          <span style="color: red" v-else-if="scope.row.eType === 1">报警</span>
          <span style="color: orange" v-else>故障</span>
        </template>
      </el-table-column>
      <el-table-column prop="reportTime" label="上报时间" min-width="90" align="center"></el-table-column>
    </el-table>
    <!--用户电器展示对话框-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      center
      fullscreen
      title="用户电器实时运行监控"
      :visible.sync="homeAppDialogVisible"
      :before-close="handleClose"
      custom-class="deep-background-02">
      <!--用户信息-->
      <div class="run-app-dialog-row-01">
        <div class="user-info">
          <!--用户头像-->
          <div>
            <img :src="person" alt="" style="height: 80px;margin: 10px">
          </div>
          <!--用户信息-->
          <div>
            <ul v-if="activeItem!==null">
              <li>
                <el-button type="primary" class="label-text-son" >绑定用户</el-button>
                <a style="color: #0dfdf3">{{activeItem.user !== null?activeItem.user.username + activeItem.user.phone:'未绑定用户'}}</a></li>
              <li>
                <el-button type="primary" class="label-text-son" >地址</el-button>
                <a style="color: #0dfdf3">{{activeItem.device.province+activeItem.device.city+activeItem.device.district+activeItem.device.township+activeItem.device.street+activeItem.device.address}}</a>
              </li>
              <li>
                <el-button type="primary" class="label-text-son" >设备识别</el-button>
                <a style="color: #0dfdf3">uuid {{activeItem.uuid}}</a>
              </li>
            </ul>
          </div>
        </div>
        <div style="width: 500px">
          <div class="back" @click="handleClose" style="cursor: pointer;float: right">返回</div>
          <div class="back" @click="handleChangeScenes(activeItem.id)" style="cursor: pointer;">场景切换 ({{scenes[currentScene].name}})</div>
        </div>
      </div>
      <homeApp :uuid="activeUuid"></homeApp>
    </el-dialog>
  </div>
</template>

<script>
  import person from '../../assets/icons/person.png'
  import { getDeviceReports } from '../../api/deviceReportNew'
  import addressComponent from '../../components/address'
  import homeApp from './components/homeApp'
  import { selectedAddressToParams } from '../../utils/AMap'
  // import { getChartInstance, getChartOptionSimpleLine } from '../../utils/charts'
  import { updateChannel } from '../../api/device'
  import { getUnbindUsers } from '../../api/user'

  const defaultSearchForm = { uuid: null, expand: 'user,device', user_id: null, eType: null }

  export default {
    components: { addressComponent, homeApp },
    data() {
      return {
        person,
        heartbeat_Time: 60000000, // 监测设备断线的延时
        char01: null,
        chartData: [],
        indexOfListForChartData: 0,
        // 刷新间隔时间
        refreshSpeed: 3000,
        ifCanSetTimeout: true,
        homeAppDialogVisible: false,
        activeUuid: 0,
        activeItem: null,
        totalCount: {
          device: 0,
          alarms: 0,
          unWork: 0
        },
        loading: false,
        timer: null,
        deviceReports: [],
        searchForm: Object.assign({}, defaultSearchForm),
        pageInfo: {
          page: 1,
          pageSize: 10,
          totalCount: 0
        },
        usersLoading: true,
        remoteUsers: [],
        scenes: [
          { id: 0, name: '交付场景' },
          { id: 1, name: '详细场景' }
        ],
        currentScene: 0
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
        return Object.assign({}, this.searchForm, addressParams, this.pageInfo)
      }
    },
    mounted() {
      this.init()
      this.refresh()
    },
    destroyed() {
      this.clearTimer()
      console.log('newInfo页面组件销毁，停止自动刷新：this.fetchReport = ', this.ifCanSetTimeout)
    },
    methods: {
      init() {
        // this.chart01 = getChartInstance('dynamicChart')
        // const chartOption = getChartOptionSimpleLine('', '电流变化', this.chartData)
        // this.chart01.setOption(chartOption)
        // const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
        // getDevicesTotalCount(Object.assign({}, addressParams, { state: 2 })).then(res => {
        //   this.totalCount.unWork = res
        // })
        // getDevicesTotalCount(Object.assign({}, addressParams, { state: 1 })).then(res => {
        //   this.totalCount.alarms = res
        // })
        // getDevicesTotalCount(Object.assign({}, addressParams)).then(res => {
        //   this.totalCount.device = res
        // })
      },
      // 获取每台设备最新一条上报信息
      fetchReport() {
        return getDeviceReports(this.finalSearchForm).then(data => {
          console.log('getDeviceReports >>', data)
          this.deviceReports = data._items
          this.pageInfo.totalCount = data._meta.totalCount
          return true
        })
      },
      setFetchReportType(params) {
        if (params === 2) {
          this.searchForm.eType = params
        } else this.searchForm.eType = null
        this.fetchReport()
      },
      /**
       * 将null值转为空字符串
       * @param value
       * @returns {*}
       */
      nullToEmpty(value) {
        if (value === null) {
          return ''
        } else {
          return value
        }
      },
      // 轮循
      refresh() {
        this.fetchReport().then(data => {
          console.log('')
          console.log('>>>=======================================>>>获取最新设备状态在: ' + new Date())
          console.log('每次决定是否重复请求（setTimeOut）时：this.ifCanSetTimeout = ', this.ifCanSetTimeout)
          if (this.ifCanSetTimeout === true) {
            this.timer = setTimeout(this.refresh, this.refreshSpeed)
          }
          return true
        })
      },
      onClickSearchBtn() {
        this.pageInfo.page = 1
        this.clearTimerAndRefresh()
      },
      showHomeApp(device) {
        this.homeAppDialogVisible = true
        this.activeUuid = device.uuid
        this.activeItem = device
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.clearTimer()
        this.clearTimerAndRefresh()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.clearTimerAndRefresh()
      },
      handleChangeScenes() {
        if (this.currentScene === 0) this.currentScene = 1
        else if (this.currentScene === 1) this.currentScene = 2
        else this.currentScene = 0
        const params = { uuid: this.activeUuid, scenesOrPortrait: this.currentScene }
        updateChannel(params).then(res => {
          if (res.bCode === 101) this.$notify({ title: '成功', message: '切换成功!', type: 'success' })
          else this.$notify.error({ title: '错误', message: res.bData })
        })
      },
      handleClose() {
        // ...
        this.activeUuid = '0'
        this.activeItem = null
        this.homeAppDialogVisible = false
        // console.log('homeAppDialog closed')
      },
      getRemoteUsers(value) {
        // ...
        getUnbindUsers({ username: value }).then(res => {
          this.remoteUsers = res
          this.usersLoading = false
        })
      },
      resetSearchForm() {
        this.searchForm = Object.assign({}, defaultSearchForm)
        this.pageInfo.page = 1
        this.clearTimerAndRefresh()
      },
      clearTimer() {
        this.ifCanSetTimeout = false
        clearTimeout(this.timer)
      },
      clearTimerAndRefresh() {
        this.indexOfListForChartData = 0
        this.chartData = []
        this.clearTimer()
        this.fetchReport()
        setTimeout(() => {
          this.ifCanSetTimeout = true
          this.refresh()
        }, this.refreshSpeed + 10)
      }
    },
    filters: {
      get_last_two_district_in_a_json_address(json_address) {
        const addArr = JSON.parse(json_address)
        return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
      }
    }
  }
</script>
<style scoped>
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .app-container-info {
  }
  .app-container-row-02{display: flex}
  .app-container-row-03{display: flex;justify-content:space-between;width: 100%}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}

  .run-app-dialog-row-01{background: rgba(170, 180, 255, 0.3);display: flex; flex-wrap: wrap; justify-content: space-between;margin: 0 auto;padding: 0 10px; height: 102px;}
  .run-app-dialog-row-01 .user-info{display: flex;font-weight: bolder;line-height: 25px;color: #aaa}
  .run-app-dialog-row-01 .back{color: #ffffff;height: 50px;width: 200px;margin:25px;border-radius: 40px;line-height: 50px;text-align: center;background: #02bfb7;font-size: 18px}
  .run-app-dialog-row-01 .back:hover{background: #0dfdf3}

  .label-text-son{width: 60px; padding: 2px; margin: 2px;font-size: small}
</style>
