<template>
  <div class="app-container-appliance">
    <div class="app-container-row-03">
      <!--筛选表单-->
        <el-form :inline="true" :model="searchForm"  class="demo-form-inline">
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
              <el-button icon="el-icon-search" type="primary" @click="onClickSearchBtn">查询</el-button>
              <el-button icon="el-icon-refresh" type="primary" @click="resetSearchForm">重置</el-button>
            </el-button-group>
          </el-form-item>
        </el-form>
      <!--用户列表分页区-->
      <div class="block" style="display: flex;justify-content: space-between">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page="pageInfo.page"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="5"
          layout="total, sizes, prev, pager, next, jumper"
          :total="pageInfo.total">
        </el-pagination>
      </div>
      <div>
        <el-table height="550" :data="devices" :header-cell-style="{textAlign: 'center'}" ref="listTable" stripe>
          <el-table-column type="expand">
            <template slot-scope="scope">
              <!--表格中嵌套表格，用户画像电器-->
              <div v-if="scope.row.homePortrait">
                <el-table :data="scope.row.homePortrait" stripe style="width: 100%">
                  <el-table-column prop="appName" label="电器" width="100"></el-table-column>
                  <el-table-column prop="ap" label="AP" width="100"></el-table-column>
                  <el-table-column prop="apAve" label="ApAve" width="100"></el-table-column>
                  <el-table-column prop="rp" label="RP" width="100"></el-table-column>
                  <el-table-column prop="rpAve" label="RpAve" width="100"></el-table-column>
                  <el-table-column label="运行状态" width="100">
                    <template slot-scope="scope">
                      <i class="el-icon-success" style="color: green" v-if="scope.row.state === 1"> 开</i>
                      <i class="el-icon-remove" style="color: #aaa" v-else> 关</i>
                    </template>
                  </el-table-column>
                  <el-table-column prop="openCount" label="电器开关次数" width="100"></el-table-column>
                  <el-table-column prop="appNum" label="电器开启台数" width="100"></el-table-column>
                </el-table>
              </div>
              <div v-else>暂无电器</div>
            </template>
          </el-table-column>
          <el-table-column label="设备信息" width="600">
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
              <div>
                <i class="el-icon-info" title="设备uuid"> {{scope.row.uuid}}</i>
              </div>
              <div>
                <i class="el-icon-location-outline" v-if="scope.row.lat">
                  {{ scope.row.city }}
                  {{ scope.row.district }}
                  {{ scope.row.township }}
                  {{ scope.row.street }}
                  {{ scope.row.address }}
                </i>
                <i v-else class="el-icon-location-outline" style="color: red"> 地址未设置</i>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="设备状态" min-width="100" align="center">
            <template slot-scope="scope">
              <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
              <i class="el-icon-warning" v-else-if="scope.row.state===1" style="color: red"> 报警</i>
              <i class="el-icon-error" v-else style="color: darkgoldenrod"> 故障</i>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="200" align="center">
            <template slot-scope="scope">
              <el-button type="primary" size="small" icon="el-icon-time" @click="showHomeApp(scope.row)">电器实时监控</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
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
                <a style="color: #0dfdf3">{{activeItem.province+activeItem.city+activeItem.district+activeItem.township+activeItem.street+activeItem.address}}</a>
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
  import { selectedAddressToParams } from '../../utils/AMap'
  import { getDevices, getDevicesTotalCount, updateChannel } from '../../api/device'
  import userApplianceRunRecords from './components/userApplianceRunRecords'
  import homeApp from './components/homeApp'
  import { getAppTotalCount } from '../../api/HomePortrait'
  import { getUnbindUsers } from '../../api/user'

  const defaultSearchForm = {
    user_id: null,
    uuid: null,
    enable: 1,
    expand: 'user,homePortrait'
  }
  export default {
    components: { userApplianceRunRecords, homeApp },
    data() {
      return {
        person,
        homeAppDialogVisible: false,
        activeUuid: 0,
        activeItem: null,
        eventTypes: {
          1: '开',
          2: '关'
        },
        searchForm: Object.assign({}, defaultSearchForm),
        devices: [],
        // 页码信息
        pageInfo: {
          pageSize: 10,
          page: 1,
          total: 0
        },
        totalCount: {
          device: 0,
          appliances: 0,
          runApps: 0
        },
        usersLoading: false,
        remoteUsers: [],
        scenes: [
          { id: 0, name: '交付场景' },
          { id: 2, name: '自动画像' }
        ],
        currentScene: 0
      }
    },
    mounted() {
      // console.clear()
      this.init()
    },
    computed: {
      default_address() {
        return this.$store.state.user.default_address
      },
      finalSearchForm() {
        return Object.assign(
          {},
          selectedAddressToParams(JSON.parse(this.default_address)),
          this.pageInfo,
          this.searchForm
        )
      }
    },
    filters: {
      get_last_two_district_in_a_json_address(json_address) {
        // console.log('json_address', json_address)
        const addArr = JSON.parse(json_address)
        return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
      }
    },
    methods: {
      init() {
        const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
        getDevicesTotalCount(addressParams).then(res => {
          this.totalCount.device = res
        })
        getAppTotalCount(addressParams).then(res => {
          this.totalCount.appliances = res
        })
        getAppTotalCount(Object.assign({ state: 1 }, addressParams)).then(res => {
          this.totalCount.runApps = res
        })
        this.fetchDevices()
      },
      fetchDevices() {
        getDevices(this.finalSearchForm).then(res => { // 获取设备列表
          this.devices = res._items
          this.pageInfo.total = res._meta.totalCount
        })
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchDevices()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchDevices()
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
      showHomeApp(device) {
        this.homeAppDialogVisible = true
        this.activeUuid = device.uuid
        this.activeItem = device
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
        this.fetchDevices()
      },
      onClickSearchBtn() {
        // console.log('this.finalSearchForm', this.finalSearchForm)
        this.fetchDevices()
      }
    }
  }
</script>
<style scoped>
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .title-tr td{color: white}
  .app-container-row-02{display: flex}
  .app-container-row-03{padding: 15px}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}

  .shadow{background: #14181e;color: #eee;border-radius: 10px;box-shadow:3px 3px 5px black;margin-bottom: 10px}

  .run-app-dialog-row-01{background: rgba(170, 180, 255, 0.3);display: flex; flex-wrap: wrap; justify-content: space-between;margin: 0 auto;padding: 0 10px; height: 102px;}
  .run-app-dialog-row-01 .user-info{display: flex;font-weight: bolder;line-height: 25px;color: #aaa}
  .run-app-dialog-row-01 .back{color: #ffffff;height: 50px;width: 200px;margin:25px;border-radius: 40px;line-height: 50px;text-align: center;background: #02bfb7;font-size: 18px}
  .run-app-dialog-row-01 .back:hover{background: #0dfdf3}

  .label-text-son{width: 60px; padding: 2px; margin: 2px;font-size: small}
</style>

