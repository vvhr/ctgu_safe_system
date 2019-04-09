<template>
  <div class="app-container">
    <!--表单区-->
    <el-form :inline="true" :model="searchForm" class="demo-form-inline">
      <el-form-item label="设备UUID">
        <el-input v-model="searchForm.uuid" placeholder="设备UUID"></el-input>
      </el-form-item>
      <el-form-item label="日期">
        <el-date-picker v-model="searchForm.day" type="date" placeholder="选择日期" value-format="yyyy-MM-dd"></el-date-picker>
      </el-form-item>
      <el-form-item label="时间范围">
        <el-time-picker is-range v-model="searchForm.timeRange" range-separator="至" start-placeholder="开始时间" end-placeholder="结束时间" placeholder="选择时间范围" value-format="HH:mm:ss"></el-time-picker>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onClickDownLoad">查询</el-button>
        <!--导出EXCEL按钮-->
        <jsonExcel :data="list" name="device_report_new.xls" style="width: 110px;display: inline">
          <el-button type="primary" :disabled="list.length<=0">导出EXCEl</el-button>
        </jsonExcel>
      </el-form-item>
    </el-form>
    <!--数据区-->
    <div style="color: blue;font-size: 22px;font-weight: bold">请先点查询，再点下载</div>
    <div style="color: green">
      当前查询条件：uuid:{{searchForm.uuid?searchForm.uuid:"请输入UUID"}} 日期：{{searchForm.day}} 开始时间：{{searchForm.timeRange[0]}} 结束时间：{{searchForm.timeRange[1]}}
      <br>
      当前结果集条数：{{list.length}}
    </div>
  </div>
</template>

<script>
  import { getDeviceReportNews } from '../../api/mongo'
  import { parseTime } from '../../utils'
  import jsonExcel from 'vue-json-excel'

  export default {
    components: { jsonExcel },
    data() {
      return {
        // ...
        searchForm: {
          uuid: null,
          day: parseTime(new Date(), '{y}-{m}-{d}'),
          timeRange: ['00:00:00', '23:59:59']
        },
        list: []
      }
    },
    mounted() {
      this.init()
    },
    methods: {
      init() {
      },
      onClickDownLoad() {
        if (this.searchForm.uuid !== null && this.searchForm.day !== null && this.searchForm.timeRange !== null) {
          getDeviceReportNews(this.searchForm).then(res => {
            this.$message({
              message: '查询完成',
              type: 'success'
            })
            this.list = res
            console.log(res)
          })
        } else {
          this.$message({
            message: '缺少参数，请完整选择表单',
            type: 'warning'
          })
        }
      }
    }
  }
</script>

<style scoped>
</style>
