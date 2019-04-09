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
          <el-table-column label="设备信息" min-width="280">
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
          <el-table-column label="地址" min-width="350">
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
          <el-table-column label="启用状态" min-width="200" align="center">
            <template slot-scope="scope">
              <el-switch
                :title="scope.row.enable===1?'已启用':'未启用'"
                :value="scope.row.enable === 1"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column label="设备状态" min-width="100" align="center">
            <template slot-scope="scope">
              <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
              <i class="el-icon-warning" v-else-if="scope.row.state===1" style="color: red"> 报警</i>
              <i class="el-icon-error" v-else style="color: darkgoldenrod"> 故障</i>
            </template>
          </el-table-column>
          <el-table-column label="操作" min-width="100" align="center">
            <template slot-scope="scope">
              <el-button @click="onClickRemoveBindBtn(scope.row.id)">解除绑定</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="未绑到该用户的设备">
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
          <el-table-column label="设备信息" min-width="280">
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
          <el-table-column label="地址" min-width="350">
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
          <el-table-column label="启用状态" min-width="200" align="center">
            <template slot-scope="scope">
              <el-switch
                :title="scope.row.enable===1?'已启用':'未启用'"
                :value="scope.row.enable === 1"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column label="设备状态" min-width="100" align="center">
            <template slot-scope="scope">
              <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
              <i class="el-icon-warning" v-else-if="scope.row.state===1" style="color: red"> 报警</i>
              <i class="el-icon-error" v-else style="color: darkgoldenrod"> 故障</i>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="200" align="center">
            <template slot-scope="scope">
              <el-button @click="onClickChangeBind(scope.row.id)" icon="el-icon-d-arrow-right" title="改绑给当前选择的用户">{{user.username}}</el-button>
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
    user_id: null,
    username: null,
    unbind: null,
    expand: 'user',
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
    name: 'userDevice',
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
      user: {
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
      user(val) {
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
        getDevices(Object.assign({}, this.pageInfo, { user_id: this.user.id, expand: 'user' })).then(res => {
          this.list = res._items
          this.pageInfo.totalCount = res._meta.totalCount
        })
      },
      fetchUnbindList() {
        getDevices(Object.assign({}, this.finalSearchForm, { unbindThisUserId: this.user.id })).then(res => {
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
        console.log('onClickChangeBind this.user.id >>', this.user.id)
        console.log('onClickRemoveBindBtn device_id >>', device_id)
        this.$prompt('请输入密码', '确定更改用户与【设备id' + device_id + '】 的绑定？', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /123456/,
          inputErrorMessage: '密码错误'
        }).then(({ value }) => {
          updateDevice({ id: device_id, user_id: this.user.id }).then(res => {
            if (replyRes(res)) {
              this.init()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '取消操作'
          })
        })
      },
      // 解除设备用户绑定
      onClickRemoveBindBtn(device_id) {
        console.log('onClickRemoveBindBtn device_id >>', device_id)
        this.$prompt('请输入密码', '确定取消该用户与【设备id' + device_id + '】 的绑定？', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /123456/,
          inputErrorMessage: '密码错误'
        }).then(({ value }) => {
          updateDevice({ id: device_id, unbind: 1 }).then(res => {
            if (replyRes(res)) {
              this.init()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '取消操作'
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
