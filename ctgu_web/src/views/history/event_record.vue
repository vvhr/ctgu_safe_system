<template>
  <div class="app-container-event-record">
    <!--筛选表单-->
    <div>
      <el-form :inline="true" :model="searchForm">
        <el-form-item label="设备imei">
          <el-input v-model="searchForm.imei" placeholder="请输入设备imei编号"></el-input>
        </el-form-item>
        <el-form-item label="用户名">
          <el-input v-model="searchForm.username" placeholder="请输入用户名"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onClickSearchBtn">查询</el-button>
          <el-button type="primary" @click="fetchList">刷新</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--分页区-->
    <div class="block" style="padding: 0 0 10px 0; display: flex">
      <el-pagination
        background
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pageInfo.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="5"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pageInfo.totalCount">
      </el-pagination>
    </div>
    <!--表格区-->
    <el-table height="550" :data="list" stripe border>
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
      <el-table-column prop="appName" label="电器名称" width="150" align="center"></el-table-column>
      <el-table-column prop="onOff" label="开启状态" width="150" align="center">
        <template slot-scope="scope">
          <span style="color: red" v-if="scope.row.onOff === 0">运行中</span>
          <span style="color: black" v-if="scope.row.onOff === 1">未开启</span>
        </template>
      </el-table-column>
      <el-table-column prop="diffLc" label="漏电流跳变(mA)" width="150" align="center"></el-table-column>
      <el-table-column prop="reportTime" label="上报时间" width="180" align="center"></el-table-column>

    </el-table>
  </div>
</template>

<script>
    import { selectedAddressToParams } from '../../utils/AMap'
    import { getEventRecord } from '../../api/eventRecord'

    export default {
      name: 'event_record',
      data() {
        return {
          // 页码信息
          pageInfo: {
            pageSize: 10,
            page: 1,
            totalCount: 0
          },
          searchForm: {
            expand: 'device,user',
            uuid: '',
            diffLc: 30,
            onOff: null
          },
          list: [],
          // 设置监测漏电跳变值
          watching_diffLc: 30,
          // 刷新间隔时间
          refreshSpeed: 3000,
          ifCanSetTimeout: true,
          // 计时器句柄
          timer: null
        }
      },
      computed: {
        default_address() {
          return this.$store.state.user.default_address
        },
        finalSearchForm() {
          this.searchForm.diffLc = this.watching_diffLc
          return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)))
        }
      },
      methods: {
        init() {
          this.fetchList()
        },
        fetchList() {
          return getEventRecord(this.finalSearchForm).then(res => {
            console.log('getEvent res>>', res)
            this.list = res._items
            this.pageInfo.totalCount = res._meta.totalCount
            // console.log(res._meta.totalCount, this.pageInfo.totalCount)
            return true
          })
        },
        handleSizeChange(size) {
          this.pageInfo.pageSize = size
          this.fetchList()
        },
        handleCurrentChange(pageNum) {
          this.pageInfo.page = pageNum
          this.fetchList()
        },
        onClickSearchBtn() {
          this.fetchList()
        },
        // 轮循
        refresh() {
          this.fetchList().then(data => {
            console.log('')
            console.log('>>>=======================================>>>获取最新设备状态在: ' + new Date())
            console.log('每次决定是否重复请求（setTimeOut）时：this.ifCanSetTimeout = ', this.ifCanSetTimeout)
            if (this.ifCanSetTimeout === true) {
              this.timer = setTimeout(this.refresh, this.refreshSpeed)
            }
            return true
          })
        }
      },
      mounted() {
        this.init()
        this.refresh()
      }
    }
</script>

<style scoped>

</style>
