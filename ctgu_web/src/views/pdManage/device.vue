<template>
  <div class="app-container-device">
    <!--筛选表单-->
    <div>
      <el-form :inline="true" :model="searchForm" size="small">
        <el-form-item label="设备uuid">
          <el-input v-model="searchForm.uuid" placeholder="请输入设备uuid编号"></el-input>
        </el-form-item>
        <el-form-item label="房间号">
          <el-input v-model="searchForm.address" placeholder="请输入房间地址"></el-input>
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
              :key="2"
              label="全部"
              :value="2">
            </el-option>
            <el-option
              :key="1"
              label="已绑定"
              :value="1">
            </el-option>
            <el-option
              :key="0"
              label="未绑定"
              :value="0">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button-group>
            <el-button type="primary" icon="el-icon-search" @click="onClickSearchBtn">查询</el-button>
            <el-button type="primary" icon="el-icon-refresh" @click="onClickResetBtn">重置</el-button>
            <!--<el-button type="success" icon="el-icon-upload2" @click="">EXCEL导出</el-button>-->
            <el-button type="success" icon="el-icon-download" @click="onClickBulkAddDeviceBtn">从实时上报表中批量导入</el-button>
          </el-button-group>
        </el-form-item>
        <el-form-item>
          <el-button>选中设备批量操作 >> </el-button>
          <!--批量绑定至用户-->
          <el-button @click="onClickBindUser" type="text" icon="el-icon-news"> 绑定至用户 </el-button>
          <!--用户搜索下拉框-->
          <el-select v-model="user_id" filterable remote reserve-keyword placeholder="请输入用户姓名关键词" :remote-method="fetchUsers" :loading="usersLoading">
            <el-option
              v-for="item in users"
              :key="item.id"
              :label="item.username + item.phone"
              :value="item.id">
            </el-option>
          </el-select>
          <!--批量绑定至项目-->
          <el-button @click="onClickBindProject" type="text" icon="el-icon-news"> 绑定至项目 </el-button>
          <!--项目搜索下拉框-->
          <el-select v-model="project_id" filterable remote reserve-keyword placeholder="请输入项目名称关键词" :remote-method="fetchProjects" :loading="loading.projects">
            <el-option
              v-for="item in projects"
              :key="item.id"
              :label="item.project_name + '： 项目编号' + item.id"
              :value="item.id">
            </el-option>
          </el-select>
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
    <!--list设备列表区-->
    <el-table height="530" :data="list" stripe border :header-cell-style="{textAlign: 'center'}" ref="listTable" @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="40">
      </el-table-column>
      <el-table-column label="设备信息" min-width="250">
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
            [房间号] {{ scope.row.address }}
          </div>
        </template>
      </el-table-column>
      <el-table-column label="地址" min-width="220">
        <template slot-scope="scope">
          <i class="el-icon-location-outline" v-if="scope.row.lat">{{ scope.row.city }} {{ scope.row.district }} {{ scope.row.township }} {{ scope.row.street }} {{ scope.row.address }}</i>
          <i class="el-icon-location-outline" v-else> 地址未设置</i>
        </template>
      </el-table-column>
      <el-table-column label="设备状态" min-width="100" align="center">
        <template slot-scope="scope">
          <i class="el-icon-success" v-if="scope.row.state===0" style="color: green"> 正常</i>
          <el-button slot="reference" size="mini" @click="onClickEditState(scope.row)" v-else-if="scope.row.state===1"><i class="el-icon-warning"  style="color: red" >
            报警
          </i></el-button>
          <el-button slot="reference" size="mini" @click="onClickEditState(scope.row)" v-else><i class="el-icon-error"  style="color: darkgoldenrod" >
            故障
          </i></el-button>
        </template>
      </el-table-column>
      <el-table-column prop="update_time" label="更新时间" min-width="160"></el-table-column>
      <el-table-column fixed="right" label="操作" width="200" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" icon="el-icon-edit" @click="onClickEditBtn(scope.row)">修改</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog
      title="住户信息修改"
      :visible.sync="visible.residentForm"
      width="30%"
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true">
      <el-form :model="ruleForm_resident" :rules="rules_resident" ref="ruleForm_resident" label-width="100px" status-icon>
        <el-form-item label="联系人" prop="contact">
          <el-input v-model="ruleForm_resident.contact"></el-input>
        </el-form-item>
        <el-form-item label="电话" prop="phone">
          <el-input v-model="ruleForm_resident.phone"></el-input>
        </el-form-item>
        <el-form-item label="单元/楼栋" prop="unit">
          <el-input v-model="ruleForm_resident.unit"></el-input>
        </el-form-item>
        <el-form-item label="房间号" prop="house_number">
          <el-input v-model="ruleForm_resident.house_number"></el-input>
        </el-form-item>
        <el-form-item label="电表号" prop="meter_number">
          <el-input v-model="ruleForm_resident.meter_number"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="submitForm_resident('ruleForm_resident')" type="primary">提 交</el-button>
        <el-button @click="visible.residentForm = false">取 消</el-button>
      </span>
    </el-dialog>
    <!--编辑设备表单-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      :title="'设备信息: ' + ruleForm.uuid" :visible.sync="visible.ruleForm" @open="handOpenSetPositionDialog" width="1500px">
      <el-row :gutter="20">
        <el-col :span="12">
          <div>
            <el-autocomplete
              v-model="selectedAddress"
              :fetch-suggestions="mapAddressSearch"
              placeholder="请输入内容"
              @select="handSearchAddress"
              value-key="fullAddress"
              style="width: 100%"
            ></el-autocomplete>
          </div>
          <div id="map-container"></div>
        </el-col>
        <el-col :span="12">
          <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
            <el-form-item label="地址" prop="address">
              <div style="border-bottom: dashed 1px #666">{{ruleForm.province + ruleForm.city + ruleForm.district + ruleForm.township + ruleForm.street}}</div>
            </el-form-item>
            <el-form-item label="详细地址" prop="address">
              <el-input v-model="ruleForm.address"></el-input>
            </el-form-item>
            <el-form-item label="项目名称" prop="project_id">
              <el-select
                v-model="ruleForm.project_id"
                filterable
                remote
                reserve-keyword
                placeholder="关键词搜索，空格表示全部"
                :remote-method="remoteMethod"
                :loading="loading.projects">
                <el-option
                  v-for="item in projects"
                  :key="item.id"
                  :label="item.project_name"
                  :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
            <!--<el-form-item label="项目id" prop="project_id">-->
              <!--<el-input v-model="ruleForm.project_id"></el-input>-->
            <!--</el-form-item>-->
            <el-form-item label="经度" prop="lon">
              <el-input v-model.number="ruleForm.lon"></el-input>
            </el-form-item>
            <el-form-item label="纬度" prop="lat">
              <el-input v-model.number="ruleForm.lat"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
              <el-button @click="visible.ruleForm = false">关闭</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
  import { getDevices, getDevice, updateDevice, bulkAddDevice, bulkBindUser, bulkBindProject } from '../../api/device'
  import { replyRes } from '../../utils/res'
  import { createMap, addMarker, selectedAddressToParams } from '../../utils/AMap'
  import { getUsers } from '../../api/user'
  import { getProjects } from '../../api/project'
  import { validatPhones } from '../../utils/validate'

  const defaultSearchForm = {
    username: null,
    unbind: 2,
    expand: 'user,project',
    uuid: null,
    // 设备状态
    state: null,
    // 排序信息
    order_by: 'id',
    order_method: 3
  }
  const defaultRuleForm = {
    project_id: null,
    user_id: null,
    address: null,
    lat: null,
    lon: null,
    province: null,
    city: null,
    citycode: null,
    district: null,
    township: null,
    street: null,
    adcode: null
  }

  const defaultRuleForm_resident = {
    id: null,
    unit: '',
    house_number: '',
    meter_number: '',
    contact: '',
    phone: ''
  }
  export default {
    data() {
      return {
        // 远程用户搜索,以及设备绑定设备相关变量
        usersLoading: true,
        users: [],
        user_id: null,
        project_id: null,
        device_ids: [],
        // 常量数组
        states: {
          '0': '正常',
          '1': '报警',
          '2': '故障'
        },
        loading: {
          projects: false
        },
        projects: {},
        selectedAddress: '',
        map: null,
        marker: null,
        position: {
          lon: 0,
          lat: 0
        },
        mapOption: {
          zoom: 18,
          // zooms: [4, 18],
          center: [111.321678, 30.723026],
          layers: [],
          viewMode: '2D'
        },
        // 对话框可见性
        visible: {
          ruleForm: false,
          channelForm: false,
          bindUserForm: false,
          residentForm: false,
          faultVisible: false
        },
        boolVar: true,
        // 设备信息列表
        list: [],
        activeDevice: {},
        // 页码信息
        pageInfo: {
          pageSize: 10,
          page: 1,
          totalCount: 0
        },
        // 查询表单
        searchForm: Object.assign({}, defaultSearchForm),
        ruleForm: Object.assign({}, defaultRuleForm),
        ruleForm_resident: Object.assign({}, defaultRuleForm_resident), // 住户信息
        rules: {
          address: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          project_id: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          region_id: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          lat: [
            { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
          ],
          lon: [
            { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
          ]
        },
        rules_resident: {
          phone: [
            { required: true, message: '请输入电话号码', trigger: 'blur' },
            { validator: validatPhones(), trigger: 'blur', message: '请输入格式正确的手机号' }
          ],
          house_number: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          contact: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          meter_number: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          unit: [
            { required: true, message: '请输入', trigger: 'blur' }
          ]
        },
        channelFormRules: {
          id: [
            { required: true, message: '请输入id', trigger: 'blur' }
          ],
          unit: [
            { required: true, message: '请输入id', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          house_number: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          meter_number: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          contact: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          phone: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      default_address() {
        return this.$store.state.user.default_address
      },
      // 最终发送到findProjectListByPage接口的查询参数
      combinedSearchForm: function() {
        let addressParams = {}
        if (this.default_address) {
          addressParams = selectedAddressToParams(JSON.parse(this.default_address))
        }
        return Object.assign(
          {},
          this.searchForm,
          this.pageInfo,
          addressParams
        )
      }
    },
    filters: {
      parseScopeRowHomeUser: function(value) {
        if (value !== null) {
          return value
        }
        return '暂无数据'
      }
    },
    mounted() {
      this.fetchDevices(this.combinedSearchForm)
      this.fetchProjects()
    },
    methods: {
      /** 调用接口获取数据改变状态方法类 **/
      fetchDevices() {
        getDevices(this.combinedSearchForm).then(res => {
          this.list = res._items
          this.pageInfo.totalCount = res._meta.totalCount
        })
      },
      fetchProjects(value = null) {
        getProjects({ project_name: value }).then(res => {
          this.projects = res._items
          this.loading.projects = false
        })
      },
      // 用户远程搜索
      fetchUsers(value) {
        getUsers({ username: value }).then(res => {
          console.log('this.users>>', res)
          this.users = res._items
          this.usersLoading = false
        })
      },
      fetchDevice(device) {
        getDevice({ id: device.id }).then(res => {
          this.ruleForm = res
        })
      },
      onClickEditBtn(device) {
        this.activeDevice = device
        this.visible.ruleForm = true
        this.fetchDevice(device)
      },
      onClickShowChannelsBtn(device) {
        if (device.subVisible !== undefined) {
          device.subVisible = !device.subVisible
        } else {
          this.fetchChannels(device).then(res => {
            device.subVisible = true
          })
        }
      },
      onClickEditResidentMessage(device) {
        this.visible.residentForm = true
        this.fetchDevice(device)
      },
      // 批量导入设备
      onClickBulkAddDeviceBtn() {
        // ...
        bulkAddDevice().then(res => {
          console.log('bulkAddDevice response>>', res)
          replyRes(res)
        })
      },
      // 搜索设备
      onClickSearchBtn() {
        console.log('this.combinedSearchForm>>', this.combinedSearchForm)
        getDevices(this.combinedSearchForm).then(res => {
          this.list = res._items
          this.pageInfo.totalCount = res._meta.totalCount
        })
      },
      onClickResetBtn() {
        this.searchForm = Object.assign({}, defaultSearchForm)
        this.fetchDevices()
      },
      // 批量绑定到用户
      onClickBindUser() {
        // console.log('this.user_id>>', this.user_id)
        // console.log('this.device_ids>>', this.device_ids)
        bulkBindUser({ user_id: this.user_id, device_ids: this.device_ids }).then(res => {
          replyRes(res)
          if (res.bData.length > 0) {
            this.fetchDevices()
          }
        })
      },
      // 批量绑定到项目
      onClickBindProject() {
        bulkBindProject({ project_id: this.project_id, device_ids: this.device_ids }).then(res => {
          replyRes(res)
          if (res.bData.length > 0) {
            this.fetchDevices()
          }
        })
      },
      /** 清除故障状态 */
      onClickEditState(row) {
        this.$confirm('是否要清除故障状态?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
          center: true
        }).then(() => {
          updateDevice({ id: row.id, state: 0, uuid: row.uuid, clear_fault: 'clear_fault' }).then(res => {
            if (replyRes(res)) {
              this.fetchDevices()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消清除'
          })
        })
      },
      // 地址搜索
      mapAddressSearch(queryString, cb) {
        // 自动搜索地点
        // eslint-disable-next-line
        AMap.plugin('AMap.Autocomplete', () => {
          // 实例化Autocomplete
          const autoOptions = {
            // city 限定城市，默认全国
            city: '全国'
          }
          // eslint-disable-next-line
          const autoComplete= new AMap.Autocomplete(autoOptions);
          if (queryString) {
            autoComplete.search(queryString, function(status, result) {
              console.log(result)
              // 搜索成功时，result即是对应的匹配数据
              for (const index in result.tips) {
                result.tips[index].fullAddress = result.tips[index].district + result.tips[index].name + result.tips[index].address
              }
              cb(result.tips)
            })
          } else {
            cb([])
          }
        })
      },
      // 点击地址搜索结果后定位
      handSearchAddress(addressObj) {
        this.ruleForm.lat = addressObj.location.lat
        this.ruleForm.lon = addressObj.location.lng
        console.log('选择搜索地址栏的地址时返回的地址对象', addressObj)
        // this.ruleForm.address = addressObj.fullAddress
        // eslint-disable-next-line
        AMap.plugin('AMap.PlaceSearch', () => {
          // eslint-disable-next-line
          const placeSearch = new AMap.PlaceSearch({ map: this.map })
          placeSearch.setCity(addressObj.adcode)
          placeSearch.search(addressObj.name)
        })
      },
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            updateDevice(this.ruleForm).then(res => {
              console.log(res)
              if (replyRes(res)) {
                this.visible.ruleForm = false
                Object.assign(this.activeDevice, res.bData)
                this.ruleForm = Object.assign({}, defaultRuleForm)
              }
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },
      handleSizeChange(size) {
        console.log('change page size', size)
        this.pageInfo.pageSize = size
        this.fetchDevices()
      },
      handleCurrentChange(pageNum) {
        console.log('change pageNum', pageNum)
        this.pageInfo.page = pageNum
        this.fetchDevices()
      },
      // 处理表格选择
      handleSelectionChange(val) {
        console.log('handleSelectionChange>>')
        const selected_device_ids = []
        val.map((v, k) => {
          // console.log(v, k)
          selected_device_ids.push(v.id)
        })
        // console.log(selected_device_ids)
        this.device_ids = selected_device_ids
      },
      handOpenSetPositionDialog() {
        this.activeDevice.lon = this.activeDevice.lon ? this.activeDevice.lon : 0
        this.activeDevice.lat = this.activeDevice.lat ? this.activeDevice.lat : 0
        // 只有第一次打开对话框时或者地图api脚本未加载时执行一次下载脚本并创建地图实例，同时也只创建一个标记物对象。后面只是移位
        if (this.map === null) {
          createMap('map-container', this.mapOption).then(mapInstance => {
            console.log('第一次调用工具成功得到地图实例')
            // 获取地图实例
            this.map = mapInstance
            // 制作第一个标记物
            this.marker = addMarker(this.map, this.activeDevice.uuid, '_marker_0', [this.activeDevice.lon, this.activeDevice.lat], (uuid) => {
              console.log(uuid)
            })
            console.log('第一次添加标记物')
            // 当点击地图时，获取地图点击坐标,并移动标记物位置
            this.map.on('click', (ev) => {
              this.ruleForm.lon = this.position.lon = ev.lnglat.lng
              this.ruleForm.lat = this.position.lat = ev.lnglat.lat
              this.marker.setPosition([this.position.lon, this.position.lat])
              // eslint-disable-next-line
              AMap.plugin('AMap.Geocoder', () => {
                // eslint-disable-next-line
                const geocoder = new AMap.Geocoder({
                  // city 指定进行编码查询的城市，支持传入城市名、adcode 和 citycode
                  city: '全国'
                })
                geocoder.getAddress([this.position.lon, this.position.lat], (status, result) => {
                  if (status === 'complete' && result.info === 'OK') {
                    console.log('使用坐标反向获取到的地址信息', result)
                    console.log('使用坐标反向获取到的地址信息', result.regeocode.addressComponent)
                    // result为对应的地理位置详细信息
                    this.ruleForm.province = result.regeocode.addressComponent.province
                    this.ruleForm.city = result.regeocode.addressComponent.city
                    this.ruleForm.district = result.regeocode.addressComponent.district
                    this.ruleForm.township = result.regeocode.addressComponent.township
                    this.ruleForm.street = result.regeocode.addressComponent.street
                    this.ruleForm.adcode = result.regeocode.addressComponent.adcode
                    this.ruleForm.citycode = result.regeocode.addressComponent.citycode
                  }
                })
              })
            })
          })
        } else {
          // 当地图实例，与唯一标记物已存在时，只需要改变标记物位置
          this.marker.setPosition([this.activeDevice.lon, this.activeDevice.lat])
        }
      },
      normalizeDevice(item) {
        // ...
        updateDevice({ state: 0, id: item.id, treatment_result: 2 }).then(res => {
          if (replyRes(res)) {
            this.fetchDevices()
          }
        })
      },
      /** 远程搜索项目列表方法 */
      remoteMethod(query) {
        this.loading.projects = true
        let params = {}
        if (query.trim() !== '') {
          params = { name: query.trim() }
        }
        getProjects(params).then(res => {
          this.projects = res._items
          this.loading.projects = false
        })
      }
    }
  }
</script>
<style>
  #map-container {border: solid 1px #a0a0a0;height: 480px; width: 100%}
  ._marker_0{color:white;background: green;width: 20px;height:20px;text-align: center;border-radius: 20px;border: solid 1px #fff;line-height: 20px}

  /** 重定义对话框样式 */
  .app-container-device {}
  .app-container-device .dialogResidentForm {}
  .app-container-device .dialogRuleForm {}
</style>
