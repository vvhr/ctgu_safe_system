<template>
  <div class="app-container">
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
    <el-table :data="list" stripe border>
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
      <el-table-column label="可疑电器" width="150">
        <template slot-scope="scope">
          <i class="el-icon-warning" v-if="scope.row.homePortrait">
            {{ scope.row.homePortrait.appName }}

          </i>
          <i class="el-icon-warning" v-else>
            {{ scope.row.typeId }}
          </i>
        </template>
      </el-table-column>
      <el-table-column prop="leakageCurrent" label="漏电流(mA)" width="150" align="center"></el-table-column>
      <el-table-column prop="optionTime" label="上报时间" width="180" align="center"></el-table-column>
      <el-table-column label="地址">
        <template slot-scope="scope">
          <i class="el-icon-location-outline" v-if="scope.row.device.city">
            {{ scope.row.device.city }}
            {{ scope.row.device.district }}
            {{ scope.row.device.township }}
            {{ scope.row.device.street }}
            {{ scope.row.device.address }}
          </i>
          <i class="el-icon-location-outline" v-else> 地址未设置</i>
        </template>
      </el-table-column>
    </el-table>

  </div>
</template>

<script>
import jsonExcel from 'vue-json-excel'
import { selectedAddressToParams } from '../../utils/AMap'
import { getLeakagecurrentEvent } from '../../api/leakagecurrentEvent'

export default {
  components: { jsonExcel },
  data() {
    return {
      // 页码信息
      pageInfo: {
        pageSize: 10,
        page: 1,
        totalCount: 0
      },
      searchForm: {
        expand: 'homePortrait,device,user',
        uuid: '',
        username: ''
      },
      list: []
    }
  },
  computed: {
    default_address() {
      return this.$store.state.user.default_address
    },
    finalSearchForm() {
      return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)))
    }
  },
  methods: {
    init() {
      this.fetchList()
    },
    fetchList() {
      getLeakagecurrentEvent(this.finalSearchForm).then(res => {
        console.log('getLeakagecurrentEvent res>>', res)
        this.list = res._items
        this.pageInfo.totalCount = res._meta.totalCount
        console.log(res._meta.totalCount, this.pageInfo.totalCount)
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
    }
  },
  mounted() {
    this.init()
  }
}
</script>
