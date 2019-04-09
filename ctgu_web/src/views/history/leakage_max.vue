<template>
  <div class="app-container">
    <!--统计最大值-->
    <div  v-loading = "loading" style="width: 100%;height: 50px">
      <el-button type="primary" @click="onClickRecursiveCreateMinMaxLcByDate">开始统计</el-button>
    </div>
    <!--查询最大值相关记录-->
    <div>
      <el-progress :text-inside="true" :stroke-width="18" :percentage="progressPercentage.minMaxLc" color="#8e71c7"></el-progress>
      <div style="height: 10px">&nbsp;</div>
      <el-progress :text-inside="true" :stroke-width="18" :percentage="progressPercentage.minMaxTpDetail" color="#8e71c7"></el-progress>
      <div style="height: 10px">&nbsp;</div>
      <el-progress :text-inside="true" :stroke-width="18" :percentage="progressPercentage.minMaxLcDetail" color="#8e71c7"></el-progress>
    </div>
    <!--Tab表格展示区-->
    <el-tabs type="card" @tab-click=""  v-model="activeTabName">
      <el-tab-pane label="极值统计表" name="first">
        <!--筛选表单-->
        <div>
          <el-form :inline="true" :model="searchForm">
            <el-form-item label="设备imei">
              <el-input v-model="searchForm.imei" placeholder="请输入设备imei编号"></el-input>
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="searchForm.username" placeholder="请输入用户名"></el-input>
            </el-form-item>
            <el-form-item label="日期">
              <el-input v-model="searchForm.date" placeholder="请输入日期"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="onClickSearchBtn">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <!--极值报表-->
        <jsonExcel :data="simpleList" name="report-min-max-lc.xls" style="width: 110px">
          <el-button type="primary">导出EXCEl</el-button>
        </jsonExcel>
        <!--分页-->
        <div>
          <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="pageInfo.page"
            :page-sizes="[500, 1000, 2000]"
            :page-size="5"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfo.total">
          </el-pagination>
        </div>
        <!--表格区-->
        <table class="_table" border="0" cellpadding="0" cellspacing="0" style="width: 100%">
          <tr>
            <th>id</th>
            <th>imei</th>
            <th>绑定用户</th>
            <th>通道1:max/min<br>漏电流mA<br>温度C</th>
            <th>通道2:max/min<br>漏电流mA<br>温度C</th>
            <th>通道3:max/min<br>漏电流mA<br>温度C</th>
            <th>通道4:max/min<br>漏电流mA<br>温度C</th>
            <th>设备地址</th>
            <th>统计日期</th>
          </tr>
          <tbody>
          <tr  v-for="(item, key) in simpleList" :key="key">
            <td>{{item.id}}</td>
            <td>{{item.imei}}</td>
            <td>{{item.user}}</td>
            <td>
              <span style="color: red">{{item.max_lc1}} / {{item.min_lc1}}</span><br><span style="color: green">{{item.max_tp1}} / {{item.min_tp1}}</span>
            </td>
            <td><span style="color: red">{{item.max_lc2}} / {{item.min_lc2}}</span><br><span style="color: green">{{item.max_tp2}} / {{item.min_tp2}}</span></td>
            <td><span style="color: red">{{item.max_lc3}} / {{item.min_lc3}}</span><br><span style="color: green">{{item.max_tp3}} / {{item.min_tp3}}</span></td>
            <td><span style="color: red">{{item.max_lc4}} / {{item.min_lc4}}</span><br><span style="color: green">{{item.max_tp4}} / {{item.min_tp4}}</span></td>
            <td>{{item.address}}</td>
            <td>{{item.date}}</td>
          </tr>
          </tbody>
        </table>
      </el-tab-pane>
      <el-tab-pane label="漏电流极值记录表(>60mA)" name="second">
        <!--筛选表单-->
        <div>
          <el-form :inline="true" :model="searchFormDetail">
            <el-form-item label="设备imei">
              <el-input v-model="searchFormDetail.imei" placeholder="请输入设备imei编号"></el-input>
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="searchFormDetail.username" placeholder="请输入用户名"></el-input>
            </el-form-item>
            <el-form-item label="日期">
              <el-input v-model="searchFormDetail.date" placeholder="请输入日期"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="onClickSearchBtnDetail">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <!--极值记录报表-->
        <jsonExcel :data="simpleListDetail" name="report-min-max-lc-detail.xls" style="width: 110px">
          <el-button type="primary">导出EXCEl</el-button>
        </jsonExcel>
        <!--分页-->
        <div>
          <el-pagination
            @size-change="handleSizeChangeDetail"
            @current-change="handleCurrentChangeDetail"
            :current-page="pageInfoDetail.page"
            :page-sizes="[200, 500, 1000, 2000]"
            :page-size="5"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfoDetail.total">
          </el-pagination>
        </div>
        <!--表格区-->
        <table class="_table" border="0" cellpadding="0" cellspacing="0" style="width: 100%">
          <tr>
            <th>id</th>
            <th>imei/通道</th>
            <th>绑定用户</th>
            <th>homeUser</th>
            <th>漏电流max/min(mA)</th>
            <th>电流(A)</th>
            <th>电压(V)</th>
            <th>温度(C)</th>
            <th>统计日期</th>
            <th>设备地址</th>
            <th>上报时间</th>
          </tr>
          <tbody>
          <tr  v-for="(item, key) in simpleListDetail" :key="key">
            <td>{{item.id}}</td>
            <td>{{item.imei}} / 通道：{{item.channel}}</td>
            <td>{{item.user}}</td>
            <td>{{item.homeUser}}</td>
            <td>{{item.leakageCurrent}} / {{item.min_leakageCurrent}}</td>
            <td>{{item.electricity}}</td>
            <td>{{item.voltage}}</td>
            <td>{{item.temperature}}</td>
            <td>{{item.date}}</td>
            <td>{{item.address}}</td>
            <td>{{item.reportTime}}</td>
          </tr>
          </tbody>
        </table>
      </el-tab-pane>
      <el-tab-pane label="温度极值记录表(>40C)" name="third">
        <!--筛选表单-->
        <div>
          <el-form :inline="true" :model="searchFormTpDetail">
            <el-form-item label="设备imei">
              <el-input v-model="searchFormTpDetail.imei" placeholder="请输入设备imei编号"></el-input>
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="searchFormTpDetail.username" placeholder="请输入用户名"></el-input>
            </el-form-item>
            <el-form-item label="日期">
              <el-input v-model="searchFormTpDetail.date" placeholder="请输入日期"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="onClickSearchBtnTpDetail">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <!--极值记录报表-->
        <jsonExcel :data="simpleListTpDetail" name="report-min-max-tp-detail.xls" style="width: 110px">
          <el-button type="primary">导出EXCEl</el-button>
        </jsonExcel>
        <!--分页-->
        <div>
          <el-pagination
            @size-change="handleSizeChangeTpDetail"
            @current-change="handleCurrentChangeTpDetail"
            :current-page="pageInfoTpDetail.page"
            :page-sizes="[200, 500, 1000, 2000]"
            :page-size="5"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfoTpDetail.total">
          </el-pagination>
        </div>
        <!--表格区-->
        <table class="_table" border="0" cellpadding="0" cellspacing="0" style="width: 100%">
          <tr>
            <th>id</th>
            <th>imei/通道</th>
            <th>绑定用户</th>
            <th>homeUser</th>
            <th>温度max/min(C)</th>
            <th>电流(A)</th>
            <th>电压(V)</th>
            <th>漏电流(mA)</th>
            <th>统计日期</th>
            <th>设备地址</th>
            <th>上报时间</th>
          </tr>
          <tbody>
          <tr  v-for="(item, key) in simpleListTpDetail" :key="key">
            <td>{{item.id}}</td>
            <td>{{item.imei}} / 通道：{{item.channel}}</td>
            <td>{{item.user}}</td>
            <td>{{item.homeUser}}</td>
            <td>{{item.temperature}} / {{item.min_temperature}}</td>
            <td>{{item.electricity}}</td>
            <td>{{item.voltage}}</td>
            <td>{{item.leakageCurrent}}</td>
            <td>{{item.date}}</td>
            <td>{{item.address}}</td>
            <td>{{item.reportTime}}</td>
          </tr>
          </tbody>
        </table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import jsonExcel from 'vue-json-excel'
  import { createMinMaxLcByDate, getMinMaxLcs } from '../../api/minMaxLc'
  import { createMinMaxLcDetailByDate, getMinMaxDetailLcs } from '../../api/minMaxLcDetail'
  import { createMinMaxTpDetailByDate, getMinMaxDetailTps } from '../../api/minMaxTpDetail'
  import { selectedAddressToParams } from '../../utils/AMap'

  export default {
    components: { jsonExcel },
    data() {
      return {
        activeTabName: 'first',
        /** 极值表搜索变量 */
        minMaxLcList: [],
        searchForm: {
          expand: 'device,user',
          imei: '',
          username: '',
          date: ''
        },
        pageInfo: {
          pageSize: 500,
          page: 1,
          total: 0
        },
        /** 漏电流极值记录表搜索变量 */
        minMaxLcDetailList: [],
        searchFormDetail: {
          expand: 'device,user,homeUser',
          imei: '',
          username: '',
          date: ''
        },
        pageInfoDetail: {
          pageSize: 200,
          page: 1,
          total: 0
        },
        /** 温度极值记录表搜索变量 */
        minMaxTpDetailList: [],
        searchFormTpDetail: {
          expand: 'device,user,homeUser',
          imei: '',
          username: '',
          date: ''
        },
        pageInfoTpDetail: {
          pageSize: 200,
          page: 1,
          total: 0
        },
        /** 执行统计时需要的变量 */
        loading: false,
        loadingLc: true,
        loadingTp: true,
        dates: [],
        progressPercentage: {
          minMaxLc: 0,
          minMaxLcDetail: 0,
          minMaxTpDetail: 0
        }
      }
    },
    mounted() {
      this.init()
    },
    computed: {
      default_address() {
        return this.$store.state.user.default_address
      },
      /** 极值表所需方法 */
      finalSearchForm() {
        return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)))
      },
      simpleList() {
        const simpleList = []
        this.minMaxLcList.forEach((v, k) => {
          simpleList.push({
            // basic
            id: v.id,
            imei: v.imei,
            max_lc1: v.max_lc1,
            min_lc1: v.min_lc1,
            max_lc2: v.max_lc2,
            min_lc2: v.min_lc2,
            max_lc3: v.max_lc3,
            min_lc3: v.min_lc3,
            max_lc4: v.max_lc4,
            min_lc4: v.min_lc4,
            max_tp1: v.max_tp1 / 10,
            min_tp1: v.min_tp1 / 10,
            max_tp2: v.max_tp2 / 10,
            min_tp2: v.min_tp2 / 10,
            max_tp3: v.max_tp3 / 10,
            min_tp3: v.min_tp3 / 10,
            max_tp4: v.max_tp4 / 10,
            min_tp4: v.min_tp4 / 10,
            created_at: v.created_at,
            date: v.date,
            // device
            address: v.device.city + v.device.district + v.device.township + v.device.street + v.device.address,
            // user
            user: (v.user === null) ? '未绑定用户' : (v.user.username + '-' + v.user.phone)
          })
        })
        return simpleList
      },
      /** 漏电流极值记录表所需方法 */
      finalSearchFormDetail() {
        return Object.assign({}, this.searchFormDetail, this.pageInfoDetail, selectedAddressToParams(JSON.parse(this.default_address)))
      },
      simpleListDetail() {
        const simpleList = []
        this.minMaxLcDetailList.forEach((v, k) => {
          simpleList.push({
            // basic
            id: v.id,
            imei: v.imei,
            channel: v.channel,
            electricity: v.electricity / 1000,
            leakageCurrent: v.leakageCurrent,
            min_leakageCurrent: v.min_leakageCurrent,
            voltage: v.voltage / 100,
            temperature: v.temperature / 10,
            reportTime: v.reportTime,
            date: v.date,
            // device
            address: v.device.city + v.device.district + v.device.township + v.device.street + v.device.address,
            // user
            user: (v.user === null) ? '未绑定用户' : (v.user.username + '-' + v.user.phone),
            // homeUser
            homeUser: (v.homeUser === null) ? '未设置homeUser记录' : (v.homeUser.contact + '-' + v.homeUser.phone + '-单元：' + v.homeUser.unit + '-门牌：' + v.homeUser.house_number)
          })
        })
        return simpleList
      },
      /** 温度极值记录表所需方法 */
      finalSearchFormTpDetail() {
        return Object.assign({}, this.searchFormTpDetail, this.pageInfoTpDetail, selectedAddressToParams(JSON.parse(this.default_address)))
      },
      simpleListTpDetail() {
        const simpleList = []
        this.minMaxTpDetailList.forEach((v, k) => {
          simpleList.push({
            // basic
            id: v.id,
            imei: v.imei,
            channel: v.channel,
            electricity: v.electricity / 1000,
            leakageCurrent: v.leakageCurrent,
            min_temperature: v.min_temperature / 10,
            voltage: v.voltage / 100,
            temperature: v.temperature / 10,
            reportTime: v.reportTime,
            date: v.date,
            // device
            address: v.device.city + v.device.district + v.device.township + v.device.street + v.device.address,
            // user
            user: (v.user === null) ? '未绑定用户' : (v.user.username + '-' + v.user.phone),
            // homeUser
            homeUser: (v.homeUser === null) ? '未设置homeUser记录' : (v.homeUser.contact + '-' + v.homeUser.phone + '-单元：' + v.homeUser.unit + '-门牌：' + v.homeUser.house_number)
          })
        })
        return simpleList
      }
    },
    methods: {
      init() {
        this.dates = this.getDates()
        this.fetchListMinMaxLc()
        this.fetchListMinMaxLcDetail()
        this.fetchListMinMaxTpDetail()
      },
      /** 极值表所需方法 */
      fetchListMinMaxLc() {
        getMinMaxLcs(this.finalSearchForm).then(res => {
          this.minMaxLcList = res._items
          this.pageInfo.total = res._meta.totalCount
        })
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchListMinMaxLc()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchListMinMaxLc()
      },
      onClickSearchBtn() {
        this.fetchListMinMaxLc()
      },

      /** 漏电流极值记录表所需方法 */
      fetchListMinMaxLcDetail() {
        getMinMaxDetailLcs(this.finalSearchFormDetail).then(res => {
          console.log(res)
          this.minMaxLcDetailList = res._items
          this.pageInfoDetail.total = res._meta.totalCount
        })
      },
      handleSizeChangeDetail(size) {
        this.pageInfoDetail.pageSize = size
        this.fetchListMinMaxLcDetail()
      },
      handleCurrentChangeDetail(pageNum) {
        this.pageInfoDetail.page = pageNum
        this.fetchListMinMaxLcDetail()
      },
      onClickSearchBtnDetail() {
        this.fetchListMinMaxLcDetail()
      },
      /** 温度极值记录表所需方法 */
      fetchListMinMaxTpDetail() {
        getMinMaxDetailTps(this.finalSearchFormTpDetail).then(res => {
          console.log(res)
          this.minMaxTpDetailList = res._items
          this.pageInfoTpDetail.total = res._meta.totalCount
        })
      },
      handleSizeChangeTpDetail(size) {
        this.pageInfoTpDetail.pageSize = size
        this.fetchListMinMaxTpDetail()
      },
      handleCurrentChangeTpDetail(pageNum) {
        this.pageInfoTpDetail.page = pageNum
        this.fetchListMinMaxLcDetail()
      },
      onClickSearchBtnTpDetail() {
        this.fetchListMinMaxTpDetail()
      },
      /** 获取从初始日期到昨天的所有日期，用于日期统计存储 */
      getDates() {
        const dates = []
        const startDate = new Date()
        startDate.setDate(startDate.getDate() - 2)
        const endDate = new Date()
        endDate.setDate(endDate.getDate() - 1)
        // eslint-disable-next-line
        while (startDate <= endDate) {
          dates.push(startDate.getFullYear() + '-' + (startDate.getMonth() + 1).toString().padStart(2, '0') + '-' + startDate.getDate().toString().padStart(2, '0'))
          startDate.setDate(startDate.getDate() + 1)
        }
        return dates
      },
      /** 以下两个方法统计最大值并存入mysql表，因为mongoDB只存三天数据，为了可靠，只统计前两天的数据 */
      onClickRecursiveCreateMinMaxLcByDate() {
        this.loading = true
        this.progressPercentage.minMaxLc = 10
        this.progressPercentage.minMaxLcDetail = 0
        this.progressPercentage.minMaxTpDetail = 0
        this.recursiveCreateMinMaxLcByDate(this.dates, 0, 0)
      },
      recursiveCreateMinMaxLcByDate(dates, datesIndex = 0, doneDays = 0) {
        createMinMaxLcByDate({ date: dates[datesIndex] }).then(res => {
          console.log('统计极值', res, dates[datesIndex])
          doneDays++
          // 计算百分比
          this.progressPercentage.minMaxLc = parseInt(doneDays / dates.length * 100)
          // 继续计算
          datesIndex++
          if (datesIndex <= dates.length - 1) {
            this.recursiveCreateMinMaxLcByDate(dates, datesIndex, doneDays)
          } else {
            this.doRecursiveCreateMinMaxLcDetailByDate()
            this.doRecursiveCreateMinMaxTpDetailByDate()
            this.$message({
              message: '极值统计完成',
              type: 'success'
            })
          }
        })
      },
      /** 以下两个方法查询漏电流最大值对应的记录，并存入mysql表 因为mongoDB只存三天数据，为了可靠，只统计前两天的数据 */
      doRecursiveCreateMinMaxLcDetailByDate() {
        this.progressPercentage.minMaxLcDetail = 0
        this.recursiveCreateMinMaxLcDetailByDate(this.dates, 0, 0)
      },
      recursiveCreateMinMaxLcDetailByDate(dates, datesIndex = 0, doneDays = 0) {
        this.loading = true
        this.loadingLc = true
        createMinMaxLcDetailByDate({ date: dates[datesIndex] }).then(res => {
          console.log('查询漏电流极值对应记录', res, dates[datesIndex])
          doneDays++
          // 计算百分比
          this.progressPercentage.minMaxLcDetail = parseInt(doneDays / dates.length * 100)
          // 继续计算
          datesIndex++
          if (datesIndex <= dates.length - 1) {
            this.recursiveCreateMinMaxLcDetailByDate(dates, datesIndex, doneDays)
          } else {
            this.loadingLc = false
            if (this.loadingTp === false) {
              this.loading = false
            }
            this.$message({
              message: '漏电流极值记录查询完成',
              type: 'success'
            })
          }
        })
      },

      /** 以下两个方法查询温度最大值对应的记录，并存入mysql表 因为mongoDB只存三天数据，为了可靠，只统计前两天的数据 */
      doRecursiveCreateMinMaxTpDetailByDate() {
        this.progressPercentage.minMaxTpDetail = 0
        this.recursiveCreateMinMaxTpDetailByDate(this.dates, 0, 0)
      },
      recursiveCreateMinMaxTpDetailByDate(dates, datesIndex = 0, doneDays = 0) {
        this.loading = true
        this.loadingTp = true
        createMinMaxTpDetailByDate({ date: dates[datesIndex] }).then(res => {
          console.log('查询温度极值对应记录', res, dates[datesIndex])
          doneDays++
          // 计算百分比
          this.progressPercentage.minMaxTpDetail = parseInt(doneDays / dates.length * 100)
          // 继续计算
          datesIndex++
          if (datesIndex <= dates.length - 1) {
            this.recursiveCreateMinMaxTpDetailByDate(dates, datesIndex, doneDays)
          } else {
            this.loadingTp = false
            if (this.loadingLc === false) {
              this.loading = false
            }
            this.$message({
              message: '温度极值记录查询完成',
              type: 'success'
            })
          }
        })
      }
    }
  }
</script>
