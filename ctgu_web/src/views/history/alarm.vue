<template>
  <div class="app-container-alarm">
    <!--列表分页区 + 查询表单-->
    <div class="app-container-row-03">
       <el-row style="padding-left: 20px;height: 45px">
        <!--查询表单-->
          <el-form :inline="true" :model="searchForm" class="demo-form-inline">
            <el-form-item label="上报时间">
              <el-date-picker
                size="small"
                v-model="searchForm.reportTime"
                type="datetimerange"
                :picker-options="pickerOptions"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期"
                align="right">
              </el-date-picker>
            </el-form-item>
            <el-form-item>
              <el-button size="small" type="primary" @click="onSubmitSearchForm">查询</el-button>
            </el-form-item>
          </el-form>
       </el-row>
       <el-row style="padding-left: 20px;height: 45px">
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
     </el-row>
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
      <el-table-column label="联系人,电话,单元/楼栋,房间号,电表号" min-width="260">
        <template slot-scope="scope">
          <div>
            <span v-if="scope.row.homeUser">
              <i class="el-icon-news"> {{ scope.row.homeUser.contact | parseScopeRowHomeUser }}</i>
              <i class="el-icon-phone-outline"> {{ scope.row.homeUser.phone | parseScopeRowHomeUser}}</i><br>
              <i class="el-icon-news"> {{ scope.row.homeUser.unit | parseScopeRowHomeUser}}</i>
              <i class="el-icon-news"> {{ scope.row.homeUser.house_number | parseScopeRowHomeUser}}</i>
              <i class="el-icon-news"> {{ scope.row.homeUser.meter_number | parseScopeRowHomeUser}}</i>
            </span>
            <span style="color: red" v-else>
              <i class="el-icon-news">暂无信息</i>
            </span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="地址" width="220">
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
      <el-table-column label="异常类型" align="center">
        <template slot-scope="scope">
          {{alarm_type[scope.row.eType]}}
        </template>
      </el-table-column>
      <el-table-column label="异常描述" align="center">
        <template slot-scope="scope">
          {{scope.row.eComment}}
        </template>
      </el-table-column>
      <el-table-column label="处理状态" align="center">
        <template slot-scope="scope">
          {{scope.row.treatment_result === 1 ? '已处理' : '未处理'}}
        </template>
      </el-table-column>
      <el-table-column label="电流(A) 电压(V) 漏电流(mA) 温度(C) 上报时间]" width="400" align="center">
        <template slot-scope="scope">
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.c/100}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.v/1000}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.lc}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.t/10}}</el-button>
          <el-button icon="el-icon-time" type="text" style="width: 100px">[{{scope.row.reportTime}}]</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
  import { selectedAddressToParams } from '../../utils/AMap'
  import { _join3 } from '../../utils/string'
  import { getDeviceExceptions } from '../../api/exceptionReportNew'
  export default {
    name: 'alarms',
    data() {
      return {
        exceptionList: [],
        selectedAddress: [],
        searchForm: {
          expand: 'device,user,homeUser',
          treatment_result: null,
          eType: null,
          reportTime: []
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
        },
        pickerOptions: {
          shortcuts: [{
            text: '最近一周',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
              picker.$emit('pick', [start, end])
            }
          }, {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
              picker.$emit('pick', [start, end])
            }
          }, {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
              picker.$emit('pick', [start, end])
            }
          }]
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
        return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)), { order_by: 'reportTime', order_method: 3 })
      }
    },
    mounted() {
      this.init()
    },
    destroyed() {
      console.clear()
    },
    methods: {
      init() {
        this.fetchLastAlarmList()
      },
      // 获取最新报警信息
      fetchLastAlarmList(params) {
        if (this.searchForm.reportTime === null) {
          this.searchForm.start_time = null
          this.searchForm.end_time = null
        } else {
          if (this.searchForm.reportTime[0] !== null) {
            this.searchForm.start_time = this.searchForm.reportTime[0]
            this.searchForm.end_time = this.searchForm.reportTime[1]
          } else {
            this.searchForm.start_time = null
            this.searchForm.end_time = null
          }
        }
        const data = Object.assign({}, this.finalSearchForm, params)
        getDeviceExceptions(data).then(res => {
          // console.log('getDeviceExceptions res>>', res)
          this.exceptionList = res._items
          this.pageInfo.total = res._meta.totalCount
          return true
        })
      },
      fetchAlarmListForHome(type) {
        if (type === 'today') {
          this.searchForm.start_time = new Date(new Date(new Date().toLocaleDateString()).getTime())
          this.searchForm.end_time = new Date(new Date(new Date().toLocaleDateString()).getTime() + 24 * 60 * 60 * 1000 - 1)
        } else if (type === 'month') {
          this.searchForm.start_time = this.getThisMonth(1)
          this.searchForm.end_time = this.getThisMonth(2)
        } else if (type === 'all') {
          this.searchForm.start_time = null
          this.searchForm.end_time = null
        }
        this.searchForm.reportTime = [this.searchForm.start_time, this.searchForm.end_time]
        const data = Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)), { order_by: 'reportTime', order_method: 3, eType: 1 })
        getDeviceExceptions(data).then(res => {
          this.exceptionList = res._items
          this.pageInfo.total = res._meta.totalCount
          return true
        })
      },
      onSubmitSearchForm() {
        this.fetchLastAlarmList()
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchLastAlarmList()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchLastAlarmList()
      },
      getThisMonth(dayType) {
        const moment = require('moment')
        // 获取当前时间
        const date = new Date()
        // 获取当前月的第一天
        const monthStart = date.setDate(1)
        // 获取当前月
        let currentMonth = date.getMonth()
        // 获取到下一个月，++currentMonth表示本月+1，一元运算
        const nextMonth = ++currentMonth
        // 获取到下个月的第一天
        const nextMonthFirstDay = new Date(date.getFullYear(), nextMonth, 1)
        // 一天时间的毫秒数
        const oneDay = 1000 * 60 * 60 * 24
        // 获取当前月第一天和最后一天
        const firstDay = moment(monthStart).format('YYYY-MM-DD')
        // nextMonthFirstDay-oneDay表示下个月的第一天减一天时间的毫秒数就是本月的最后一天
        const lastDay = moment(nextMonthFirstDay - oneDay).format('YYYY-MM-DD')
        if (dayType === 1) return firstDay
        else return lastDay
      }
    }
  }
</script>
<style scoped>
  .app-container-alarm{
    padding: 10px;
  }
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .title-tr td{color: white}
  .app-container-row-02{display: flex}
  .app-container-row-03{width: 100%}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}
</style>
