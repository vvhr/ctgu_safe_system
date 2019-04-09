<template>
  <div class="app-container-sendMessage">
    <!--分页区-->
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
    <!--list设备列表区-->
    <!--表格区-->
    <el-table :data="list" stripe height="640">
      <el-table-column prop="id" label="ID" width="100"></el-table-column>
      <el-table-column prop="receiver" label="接收者"></el-table-column>
      <el-table-column prop="message" label="消息内容" width="450"></el-table-column>
      <el-table-column align="center" prop="type" label="类型" width="80"></el-table-column>
      <el-table-column align="center" prop="send_state" label="状态" width="80"></el-table-column>
      <el-table-column prop="send_time" label="发送时间"></el-table-column>
      <el-table-column prop="imei" label="imei"></el-table-column>
      <el-table-column align="center" prop="channel" label="通道" width="80"></el-table-column>
    </el-table>
  </div>
</template>

<script>
  import { getSmsList } from '../../api/smsLog'
  export default {
    data() {
      return {
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
      this.fetchSmsList()
    },
    methods: {
      // 获取最新报警信息
      fetchSmsList() {
        return getSmsList(this.pageInfo).then(res => {
          this.list = res._items
          this.pageInfo.total = res._meta.totalCount
        })
      },
      // ---------------------------------分页相关方法
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchSmsList()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchSmsList()
      }
    }
  }
</script>
<style scoped>
  .app-container-sendMessage {
    padding: 10px;
  }
</style>
