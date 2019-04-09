<template>
  <div class="app-container-riskApp">
    <div style="width: 100%;">
      <!--分页-->
      <div>
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page="pageInfo.page"
          :page-sizes="[5, 10, 20, 100]"
          :page-size="5"
          layout="total, sizes, prev, pager, next, jumper"
          :total="pageInfo.total">
        </el-pagination>
      </div>
      <el-table height="620" :data="applianceRunRecordsList" style="width: 100%;margin-top: 30px" border :stripe="true" row-key="id">
        <el-table-column label="电器名称" width="150">
          <template slot-scope="scope">
            {{scope.row.appName === null? '未知电器':scope.row.appName}}
          </template>
        </el-table-column>
        <el-table-column label="高危电器" width="120">
          <template slot-scope="scope">
            <i class="el-icon-warning" style="color: red" v-if="scope.row.is_high === 1">高危电器</i>
            <i class="" v-else style="color: green">正常电器</i>
          </template>
        </el-table-column>
        <el-table-column label="开关状态" width="100">
          <template slot-scope="scope">
            <i class="el-icon-success" style="color: green" v-if="scope.row.state === 1"> 开</i>
            <i class="el-icon-remove" style="color: #aaa" v-else> 关</i>
          </template>
        </el-table-column>
        <el-table-column label="设备uuid" width="250">
          <template slot-scope="scope">
            <i class="el-icon-news">{{ scope.row.uuid }}</i>
          </template>
        </el-table-column>
        <el-table-column label="地址" width="250">
          <template slot-scope="scope">
            <div v-if="scope.row.device !== null">
              <i class="el-icon-location-outline"></i>
              {{scope.row.device.province}}
              {{scope.row.device.city}}
              {{scope.row.device.district}}
              {{scope.row.device.township}}
            </div>
            <div v-else>未找到关联设备</div>
          </template>
        </el-table-column>
        <el-table-column label="详细地址" width="250">
          <template slot-scope="scope">
            <div v-if="scope.row.device !== null">
              <i class="el-icon-location-outline"></i>
              {{scope.row.device.address}}
            </div>
            <div v-else>未找到关联设备</div>
          </template>
        </el-table-column>
        <el-table-column label="更新时间" width="200">
          <template slot-scope="scope">
            <i class="el-icon-time">{{ scope.row.updateTime }}</i>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <!--<div style="width: 100%;height: 50px;line-height: 50px;text-align: center"> <el-switch-->
      <!--v-model="setMonthOrYearVisible"-->
      <!--@change="onChangeMonthOrYear"-->
      <!--active-text="按年统计"-->
      <!--inactive-text="按月统计">-->
    <!--</el-switch></div>-->
    <!--<div class="app-container-row-03">-->
      <!--<div id="riskAppPieChart" ></div>-->
      <!--<div id="appTypePieChart"></div>-->
    <!--</div>-->
  </div>
</template>

<script>
import { selectedAddressToParams } from '../../utils/AMap'
import { getHomePortraits } from '../../api/HomePortrait'

export default {
  data() {
    return {
      chartInstance01: null,
      chartInstance02: null,
      chartInstance03: null,
      applianceRunRecordsList: [], /** 高危电器开启记录*/
      applianceRunRecordsCount: {}, /** 高危电器区域分组统计*/
      dangerDeviceAddress: {}, /** 高危电器开关区域*/
      setMonthOrYearVisible: false,
      pageInfo: {
        pageSize: 5,
        page: 1,
        total: 0
      },
      totalCount: {
        dangerDevice: 0,
        device: 0,
        runApps: 0
      },
      timeType: 1,
      chartTitle: '辖区高危电器开关次数占比图(月)'
    }
  },
  mounted() {
    this.init()
  },
  computed: {
    default_address() {
      return this.$store.state.user.default_address
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
      const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
      getHomePortraits(Object.assign({}, addressParams, { is_high: 1, state: 1 })).then(res => {
        this.applianceRunRecordsList = res._items
        console.log(res._items)
      })
    },
    handleSizeChange(size) {
      this.pageInfo.pageSize = size
      // this.fetchApplianceRunRecords()
    },
    handleCurrentChange(pageNum) {
      this.pageInfo.page = pageNum
      // this.fetchApplianceRunRecords()
    }
  }
}
</script>
<style scoped>
  .app-container-riskApp { overflow: auto}
  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}

</style>
