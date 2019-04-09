<template>
  <div>
    <!--用户电器运行记录分页区-->
    <div class="block">
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
    <table class="_table" border="0" cellpadding="0" cellspacing="0">
      <tbody style="background: #f0f0f0;">
      <tr v-for="runRecord in list" :key="runRecord.id">
        <td>{{eventTypes[runRecord.eventType]}}</td>
        <td>{{runRecord.eventTime}}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  import { getApplianceRunRecords } from '../../../api/analysisResults'

  export default {
    name: 'userApplianceRunRecords',
    props: {
      home_appliance: {
        type: Object,
        required: true,
        default() {
          return {
            // home_id
            homeId: 0,
            // 电器指纹ID 依赖这两个在电器运行表中查相关记录
            typeId: 0
          }
        }
      }
    },
    data() {
      return {
        eventTypes: {
          1: '开',
          2: '关'
        },
        list: [],
        // 页码信息
        pageInfo: {
          pageSize: 10,
          page: 1,
          total: 0
        }
      }
    },
    mounted() {
      this.fetchUserApplianceRunRecords()
    },
    methods: {
      fetchUserApplianceRunRecords() {
        getApplianceRunRecords(Object.assign({}, this.pageInfo, { homeId: this.home_appliance.homeId, pid: this.home_appliance.id })).then(res => {
          // console.log('fetchUserApplianceRunRecords:getApplianceRunRecords:res', res)
          this.list = res._items
          this.pageInfo.total = res._meta.totalCount
        })
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchUserApplianceRunRecords()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchUserApplianceRunRecords()
      }
    }
  }
</script>
