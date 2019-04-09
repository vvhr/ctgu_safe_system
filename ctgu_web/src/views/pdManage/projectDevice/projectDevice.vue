<template>
  <div>
    <!--list已绑设备列表区-->
    <el-tabs type="border-card">
      <el-tab-pane label="已绑设备">
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
        <el-table :data="list" stripe border :header-cell-style="{textAlign: 'center'}">
          <el-table-column label="设备信息">
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
              <div>
                <span v-if="scope.row.project">
                  <i class="el-icon-news"> {{ scope.row.project.project_name}}</i>
                </span>
                <span style="color: red" v-else>
                  <i class="el-icon-news" style="color: red"> 未绑定项目</i>
                </span>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="地址">
            <template slot-scope="scope">
              <i class="el-icon-location-outline" v-if="scope.row.lat">
                {{ scope.row.city }}
                {{ scope.row.district }}
                {{ scope.row.township }}
                {{ scope.row.street }}
                {{ scope.row.address }}
              </i>
              <i class="el-icon-location-outline" v-else> 地址未设置</i>
            </template>
          </el-table-column>
          <el-table-column label="启用状态" align="center">
            <template slot-scope="scope">
              <el-switch
                :title="scope.row.enable===1?'已启用':'未启用'"
                :value="scope.row.enable === 1"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column label="设备状态" align="center">
            <template slot-scope="scope">
              <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
              <i class="el-icon-warning" v-else-if="scope.row.state===1" style="color: red"> 报警</i>
              <i class="el-icon-error" v-else style="color: darkgoldenrod"> 故障</i>
            </template>
          </el-table-column>
          <el-table-column label="操作" align="center">
            <template slot-scope="scope">
              <el-button @click="onClickRemoveBindBtn(scope.row.id)">解除绑定</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="未绑到该项目的设备">
        <!--筛选表单-->
        <div>
          <el-form :inline="true" :model="searchForm" size="mini">
            <el-form-item label="设备uuid">
              <el-input v-model="searchForm.uuid" placeholder="请输入设备uuid编号"></el-input>
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="searchForm.username" placeholder="请输入用户名"></el-input>
            </el-form-item>
            <el-form-item label="启用状态">
              <el-select v-model="searchForm.enable" placeholder="请选择">
                <el-option :key="1" label="启用" value="1"></el-option>
                <el-option :key="0" label="禁用" value="0"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="运行状态">
              <el-select v-model="searchForm.state" placeholder="请选择">
                <el-option
                  v-for="(v,k) in states"
                  :key="k"
                  :label="v"
                  :value="k">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="是否绑定用户">
              <el-select v-model="searchForm.unbind" placeholder="请选择" style="width: 100px">
                <el-option
                  :key="1"
                  label="未绑定"
                  :value="1">
                </el-option>
                <el-option
                  :key="0"
                  label="全部"
                  :value="0">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button-group>
                <el-button type="primary" icon="el-icon-search" @click="onClickSearchBtn">查询</el-button>
                <el-button type="primary" icon="el-icon-refresh" @click="onClickResetBtn">重置</el-button>
              </el-button-group>
            </el-form-item>
          </el-form>
        </div>
        <!--分页区-->
        <div class="block" style="padding: 0 0 10px 0; display: flex">
          <el-pagination
            background
            @size-change="handleSizeChangeUnbind"
            @current-change="handleCurrentChangeUnbind"
            :current-page="pageInfoUnbind.page"
            :page-sizes="[10, 20, 50, 100]"
            :page-size="5"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfoUnbind.totalCount">
          </el-pagination>
        </div>
        <el-table :data="unbindList" stripe border :header-cell-style="{textAlign: 'center'}">
          <el-table-column label="设备信息">
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
              <div>
                <span v-if="scope.row.project">
                  <i class="el-icon-news"> {{ scope.row.project.project_name}}</i>
                </span>
                <span style="color: red" v-else>
                  <i class="el-icon-news" style="color: red"> 未绑定项目</i>
                </span>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="地址">
            <template slot-scope="scope">
              <i class="el-icon-location-outline" v-if="scope.row.lat">
                {{ scope.row.city }}
                {{ scope.row.district }}
                {{ scope.row.township }}
                {{ scope.row.street }}
                {{ scope.row.address }}
              </i>
              <i class="el-icon-location-outline" v-else> 地址未设置</i>
            </template>
          </el-table-column>
          <el-table-column label="启用状态" align="center">
            <template slot-scope="scope">
              <el-switch
                :title="scope.row.enable===1?'已启用':'未启用'"
                :value="scope.row.enable === 1"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column label="设备状态" align="center">
            <template slot-scope="scope">
              <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
              <i class="el-icon-warning" v-else-if="scope.row.state===1" style="color: red"> 报警</i>
              <i class="el-icon-error" v-else style="color: darkgoldenrod"> 故障</i>
            </template>
          </el-table-column>
          <el-table-column label="绑定到项目" width="200" align="center">
            <template slot-scope="scope">
              <el-button @click="onClickChangeBind(scope.row.id)" icon="el-icon-d-arrow-right" title="改绑给当前选择的项目">{{project.project_name}}</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import { getDevices, updateDevice } from '../../../api/device'
  import { replyRes } from '../../../utils/res'

  const defaultSearchForm = {
    project_id: null,
    username: null,
    unbind: null,
    expand: 'user,project',
    imei: null,
    // 设备状态
    state: null,
    // 启用状态
    enable: null,
    // 排序信息
    order_by: 'id',
    order_method: 3
  }
  export default {
    name: 'projectDevice',
    data() {
      return {
        list: [],
        unbindList: [],
        activeDevice: {},
        // 页码信息
        pageInfo: {
          pageSize: 5,
          page: 1,
          totalCount: 0
        },
        pageInfoUnbind: {
          pageSize: 5,
          page: 1,
          totalCount: 0
        },
        searchForm: Object.assign({}, defaultSearchForm),
        // 常量数组
        states: {
          '0': '正常',
          '1': '报警',
          '2': '故障'
        }
      }
    },
    props: {
      project: {
        type: Object,
        required: true,
        default() {
          return {}
        }
      }
    },
    mounted() {
      console.log('mounted')
      this.init()
    },
    computed: {
      // 专为未绑定设备搜索服务
      finalSearchForm() {
        return Object.assign({}, this.pageInfoUnbind, this.searchForm)
      }
    },
    watch: {
      project(val) {
        console.log('watch', val)
        this.init()
      }
    },
    methods: {
      init() {
        this.fetchList()
        this.fetchUnbindList()
      },
      fetchList() {
        getDevices(Object.assign({}, { project_id: this.project.id, expand: 'user,project' }, this.pageInfo)).then(res => {
          console.log('getDevices res>>', res)
          this.list = res._items
          this.pageInfo.totalCount = res._meta.totalCount
        })
      },
      fetchUnbindList() {
        getDevices(Object.assign({}, this.finalSearchForm, { unbindThisProjectId: this.project.id })).then(res => {
          console.log('fetchUnbindList res>>', res)
          this.unbindList = res._items
          this.pageInfoUnbind.totalCount = res._meta.totalCount
        })
      },
      onClickSearchBtn() {
        this.fetchUnbindList()
      },
      onClickResetBtn() {
        this.searchForm = Object.assign({}, defaultSearchForm)
        this.fetchUnbindList()
      },
      // 将设备绑定到当前用户
      onClickChangeBind(device_id) {
        console.log('onClickChangeBind this.project.id >>', this.project.id)
        console.log('onClickRemoveBindBtn device_id >>', device_id)
        this.$confirm('确定更改【设备id' + device_id + '】 的绑定到项目[' + this.project.project_name + ']？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          updateDevice({ id: device_id, project_id: this.project.id }).then(res => {
            if (replyRes(res)) {
              this.init()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '操作取消'
          })
        })
      },
      // 解除设备用户绑定
      onClickRemoveBindBtn(device_id) {
        console.log('onClickRemoveBindBtn device_id >>', device_id)
        this.$confirm('确定取消该项目与【设备id' + device_id + '】 的绑定？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          updateDevice({ id: device_id, unbindProject: 1 }).then(res => {
            if (replyRes(res)) {
              this.init()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '操作取消'
          })
        })
      },
      // 已绑定列表分页处理
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchList()
      },
      handleCurrentChange(page) {
        this.pageInfo.page = page
        this.fetchList()
      },
      // 未绑定列表页码处理
      handleSizeChangeUnbind(size) {
        this.pageInfoUnbind.pageSize = size
        this.fetchUnbindList()
      },
      handleCurrentChangeUnbind(page) {
        this.pageInfoUnbind.page = page
        this.fetchUnbindList()
      }
    }
  }
</script>
