<template>
  <div class="app-container-alarm">
    <!--列表分页区 + 查询表单-->
    <div class="app-container-row-03">
      <!--分页-->
      <div>
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
      <!--查询表单-->
      <div>
        <el-form :inline="true" :model="searchForm" class="demo-form-inline">
          <el-form-item label="异常类型">
            <el-select v-model="searchForm.eType" placeholder="异常类型">
              <el-option label="报警" :value="1"></el-option>
              <el-option label="故障" :value="2"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmitSearchForm">查询</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <!--表格区-->
    <el-table height="550" :data="exceptionList" stripe border :header-cell-style="{textAlign: 'center'}">
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
      <el-table-column label="电流(A) 电压(V) 漏电流(mA) 温度(C) 上报时间]" width="600" align="left">
        <template slot-scope="scope">
          <div>
            <el-button icon="el-icon-info" type="text" style="width: 60px">{{scope.row.c/1000}}A</el-button>
            <el-button icon="el-icon-info" type="text" style="width: 60px">{{scope.row.v/100}}V</el-button>
            <el-button icon="el-icon-info" type="text" style="width: 60px">{{scope.row.lc}}mA</el-button>
            <el-button icon="el-icon-info" type="text" style="width: 60px">{{scope.row.t/10}}C</el-button>
            <el-button icon="el-icon-info" type="text" style="width: 120px">{{scope.row.diffLc}}mA-漏电差</el-button>
            <el-button icon="el-icon-info" type="text" style="width: 120px">{{scope.row.diffP}}w-有功差</el-button>
          </div>
          <!--地址-->
          <div>
            <i class="el-icon-location-outline" v-if="scope.row.device.lat">
              {{ scope.row.device.city }}
              {{ scope.row.device.district }}
              {{ scope.row.device.township }}
              {{ scope.row.device.street }}
              {{ scope.row.device.address }}
            </i>
            <i class="el-icon-location-outline" v-else> 地址未设置</i>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="异常描述" align="center" width="200">
        <template slot-scope="scope">
          <el-button icon="el-icon-time" type="text">[{{scope.row.reportTime}}]</el-button> <br>
          {{scope.row.eType | eTypeFilter}} -
          {{scope.row.eDetailType | eDetailTypeFilter}} -
          {{scope.row.eComment}}
        </template>
      </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          {{scope.row.treatment_result === 1 ? '已处理' : '未处理'}}
          <div v-if="scope.row.wxUsers.length>0">
            <div v-for="wxUser in scope.row.wxUsers" style="display: flex">
              <img :src="wxUser.headimgurl" alt="用户头像" style="width: 30px;height: 30px">
              &nbsp;
              <el-switch
                @change="onSwitchEnableReceiveMsg(wxUser)"
                title="是否接收事件推送"
                style="display: block"
                v-bind:value="wxUser.enable_receive_msg === 1"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
              &nbsp;
              {{wxUser.nickname}}
            </div>
          </div>
          <div v-else>
            无绑定的微信号
          </div>
          <div>
            <el-button @click="onClickSendWxMsg(scope.row)" size="mini">微信推送</el-button>
          </div>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
  import { selectedAddressToParams } from '../../utils/AMap'
  import { _join3 } from '../../utils/string'
  import { getDeviceExceptions } from '../../api/exceptionReportNew'
  import { replyRes } from '../../utils/res'
  import { sendWxAlarmMessage, enableOrNotReceiveMsg } from '../../api/wxUser'

  export default {
    name: 'alarmsManage',
    data() {
      return {
        ifCanSetTimeout: true,
        refreshSpeed: 3000,
        timer: null,
        exceptionList: [],
        selectedAddress: [],
        searchForm: {
          expand: 'device,user,wxUsers',
          treatment_result: 0,
          eType: null
        },
        // 页码信息
        pageInfo: {
          pageSize: 10,
          page: 1,
          total: 0
        },
        totalCount: {
          device: 0,
          alarms: 0,
          unWork: 0
        }
      }
    },
    filters: {
      _join3(value) {
        // return 'aaa'
        return _join3(value)
      },
      get_last_two_district_in_a_json_address(json_address) {
        const addArr = JSON.parse(json_address)
        return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
      },
      parseScopeRowHomeUser: function(value) {
        if (value !== null) {
          return value
        }
        return '暂无数据'
      },
      eTypeFilter(value) {
        if (value === 1) {
          return '报警'
        } else if (value === 2) {
          return '故障'
        } else if (value === 0) {
          return '正常'
        } else return '其他'
      },
      eDetailTypeFilter(value) {
        if (value === 1) {
          return '漏电流'
        } else if (value === 2) {
          return '温度'
        } else if (value === 3) {
          return '电流'
        } else if (value === 4) {
          return '电压'
        } else return '其他'
      }
    },
    computed: {
      // 记得一定要用函数的形式
      alarm_type() {
        return this.$store.state.app.alarm_type
      },
      alarm_cause() {
        return this.$store.state.app.alarm_cause
      },
      default_address() {
        return this.$store.state.user.default_address
      },
      finalSearchForm() {
        return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)))
      }
    },
    mounted() {
      this.refresh()
    },
    destroyed() {
      console.log('页面销毁！！！')
      this.clearTimer()
      console.clear()
    },
    methods: {
      // 获取最新报警信息
      fetchLastAlarmList(params) {
        this.searchForm.eType = params
        return getDeviceExceptions(this.finalSearchForm).then(res => {
          // console.log('getDeviceExceptions res>>', res)
          this.exceptionList = res._items
          this.pageInfo.total = res._meta.totalCount
          return true
        })
      },
      onSubmitSearchForm() {
        this.clearTimerAndRefresh()
      },
      onClickSendWxMsg(deviceExceptionNew) {
        this.$prompt('请输入密码', '确定推送微信？', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /123456/,
          inputErrorMessage: '密码错误'
        }).then(({ value }) => {
          sendWxAlarmMessage({ device_exception_new_id: deviceExceptionNew.id }).then(res => {
            console.log('onClickSendWxMsg:res', res)
            replyRes(res)
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '取消操作'
          })
        })
      },
      onSwitchEnableReceiveMsg(wxUser) {
        enableOrNotReceiveMsg({ id: wxUser.id }).then(res => {
          if (replyRes(res)) {
            wxUser.enable_receive_msg = res.bData.enable_receive_msg
          }
        })
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchLastAlarmList()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchLastAlarmList()
      },
      // 轮循
      refresh() {
        this.fetchLastAlarmList().then(data => {
          console.log('')
          console.log('==========================获取最新报警信息在: ' + new Date() + '????this.ifCanSetTimeout = ' + this.ifCanSetTimeout, this.timer)
          if (this.ifCanSetTimeout === true) {
            this.timer = setTimeout(this.refresh, this.refreshSpeed)
            return true
          }
        })
      },
      clearTimer() {
        this.ifCanSetTimeout = false
        console.log('clearTimer:this.ifCanSetTimeout', this.ifCanSetTimeout, this.timer)
        clearTimeout(this.timer)
      },
      clearTimerAndRefresh() {
        this.clearTimer()
        this.fetchLastAlarmList()
        setTimeout(() => {
          this.ifCanSetTimeout = true
          this.refresh()
        }, this.refreshSpeed + 10)
      }
    }
  }
</script>
<style scoped>
  .app-container-alarm {}
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .title-tr td{color: white}
  .app-container-row-02{display: flex}
  .app-container-row-03{display: flex;justify-content:space-between;width: 100%}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}
</style>
