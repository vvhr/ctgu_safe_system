<template>
  <div class="app-container-alarmManage">
    <!--列表分页区 + 查询表单-->
    <div class="app-container-row-03">
      <!--分页-->
      <div>
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
      <!--查询表单-->
      <div>
        <el-form :inline="true" :model="searchForm" class="demo-form-inline" size="mini">
          <el-form-item label="用户名" v-if="permissionName === 'admin'">
            <el-input v-model="searchForm.username" placeholder="请输入用户名"></el-input>
          </el-form-item>
          <el-form-item label="异常类型">
            <el-select v-model="searchForm.type" placeholder="异常类型">
              <el-option label="全部" :value="null"></el-option>
              <el-option label="报警" :value="1"></el-option>
              <el-option label="故障" :value="2"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="处理状态">
            <el-select v-model="searchForm.treatment_result" placeholder="处理状态">
              <el-option label="全部" :value="null"></el-option>
              <el-option label="未处理" :value="0"></el-option>
              <el-option label="已处理" :value="1"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmitSearchForm">查询</el-button>
            <el-button type="primary" @click="onchangeShowOrHidden">{{isShowOrHidden === true ? "展开" : "隐藏"}}</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <!--表格区-->
    <el-table height="550" class="alarm_table" :data="exceptionList" stripe :header-cell-style="{textAlign: 'center'}" :border="true" :row-key="rowKey" :expand-row-keys="isShowOrHidden === true ? expand_row_keys : []">
      <el-table-column type="expand" >
        <template slot-scope="scope">
          <!--表格中嵌套表格，维护记录-->
          <el-table :data="scope.row.maintainRecord" style="width: 99%" :stripe="false" :border="true" :header-cell-style="{textAlign: 'center'}" align="center" >
            <el-table-column prop="id" label="序号" width="60" align="center"></el-table-column>
            <el-table-column label="维护人信息" width="180" align="center">
              <template slot-scope="scope">
                <i class="el-icon-news">{{scope.row.maintainer}}</i>
                <i class="el-icon-phone-outline">{{scope.row.phone}}</i>
              </template>
            </el-table-column>
            <el-table-column prop="maintain_type" label="维护类型" :formatter="parseType" width="90" align="center"></el-table-column>
            <el-table-column prop="maintain_message" label="维护消息" :show-overflow-tooltip="true"></el-table-column>
            <el-table-column prop="maintain_suggest" label="维护建议" :show-overflow-tooltip="true"></el-table-column>
            <el-table-column
              label="图片1"
              width="120">
              <template slot-scope="scope">
                <div v-if="scope.row.imgUrl0 !== null && scope.row.imgUrl0 !== ''"  class="tooltip">
                  <el-tooltip content="提示:点击图片放大,点击底部删除" placement="top" width="100%">
                    <img  width="100px" height="50px" :src="apiBasicUrl + scope.row.imgUrl0" alt="" @click="handleBigImg(scope.row.imgUrl0)" class="img" >
                  </el-tooltip>
                  <div @click="onDialogDeleteImg({id:scope.row.id, imageFieldName: 'imgUrl0'})" class="el-icon-delete" ></div>
                </div>
                  <el-button v-else @click="onDialogUploadImg({id: scope.row.id, imageFieldName: 'imgUrl0'})" size="mini" type="primary" icon="el-icon-upload">上传图片</el-button>
              </template>
            </el-table-column>
            <el-table-column
              label="图片2"
              width="120">
              <template slot-scope="scope">
                <div v-if="scope.row.imgUrl1 !== null && scope.row.imgUrl1 !== ''"  class="tooltip">
                  <el-tooltip content="提示:点击图片放大,点击底部删除" placement="top" width="100%">
                    <img  width="100px" height="50px" :src="apiBasicUrl + scope.row.imgUrl1" alt="" @click="handleBigImg(scope.row.imgUrl1)" class="img" >
                  </el-tooltip>
                  <div @click="onDialogDeleteImg({id:scope.row.id, imageFieldName: 'imgUrl1'})" class="el-icon-delete" ></div>
                </div>
                  <el-button v-else style="position: relative;" @click="onDialogUploadImg({id: scope.row.id, imageFieldName: 'imgUrl1'})" size="mini" type="primary" icon="el-icon-upload">上传图片</el-button>
              </template>
            </el-table-column>
            <el-table-column
              label="图片3"
              width="120">
              <template slot-scope="scope">
                <div v-if="scope.row.imgUrl2 !== null && scope.row.imgUrl2 !== ''"  class="tooltip">
                  <el-tooltip content="提示:点击图片放大,点击底部删除" placement="top" width="100%">
                    <img width="100px" height="50px" :src="apiBasicUrl + scope.row.imgUrl2" alt="" @click="handleBigImg(scope.row.imgUrl2)" class="img" >
                  </el-tooltip>
                  <div @click="onDialogDeleteImg({id:scope.row.id, imageFieldName: 'imgUrl2'})" class="el-icon-delete" ></div>
                </div>
                  <el-button  v-else @click="onDialogUploadImg({id: scope.row.id, imageFieldName: 'imgUrl2'})" size="mini" type="primary" icon="el-icon-upload">上传图片</el-button>
              </template>
            </el-table-column>
            <el-table-column
              label="文件(点击红叉删除)" width="150"
              :show-overflow-tooltip="true">
              <template slot-scope="scope">
                <div v-if="scope.row.world_excel_url !== null && scope.row.world_excel_url !== ''" class="tooltip">
                  <i class="el-icon-circle-close" style="color: red;" @click="onDialogDeleteImg({id:scope.row.id, imageFieldName: 'world_excel_url'})"></i><a :href="apiBasicUrl + scope.row.world_excel_url"><i class="el-icon-document"></i >{{scope.row.world_excel_url | parseFile}}</a>
                </div>
                <div v-else>
                  <el-button type="success" size="mini" @click="onDialogUploadWordOrExcel({id: scope.row.id, imageFieldName: 'world_excel_url'})" icon="el-icon-upload2">上传文件</el-button>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="maintain_suggest" label="操作" width="70">
              <template slot-scope="scope">
                <el-button @click="onclickDialogEditMaintainRecord(scope.row)" type="primary" size="mini" icon="el-icon-edit"></el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>
      <el-table-column label="用户" width="300">
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
            <i class="el-icon-location-outline" v-if="scope.row.device.lat">
            {{ scope.row.device.city }}
            {{ scope.row.device.district }}
            {{ scope.row.device.township }}
            {{ scope.row.device.street }}
            {{ scope.row.device.address }}
          </i>
            <i class="el-icon-location-outline" v-else> 地址未设置</i>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="电流(A) 电压(V) 漏电流(mA) 温度(C) 上报时间]" width="450">
        <template slot-scope="scope">
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.c/100}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.v/100}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.lc}}</el-button>
          <el-button icon="el-icon-info" type="text" style="width: 50px">{{scope.row.t/10}}</el-button>
          <el-button icon="el-icon-time" type="text">[{{scope.row.reportTime}}]</el-button>
        </template>
      </el-table-column>
      <el-table-column label="异常类型" align="center">
        <template slot-scope="scope">
          <i class="el-icon-warning" v-if="scope.row.eType===1" style="color: darkgoldenrod"> 报警</i>
          <i class="el-icon-error" v-else-if="scope.row.eType===2" style="color: red"> 故障</i>
          <i class="el-icon-info" v-else style="color: red"> 未知</i>
        </template>
      </el-table-column>
      <el-table-column label="异常描述" align="center">
        <template slot-scope="scope">
          {{scope.row.eComment}}
        </template>
      </el-table-column>
      <el-table-column label="处理状态" align="center">
        <template slot-scope="scope">
          <el-button  v-if="scope.row.treatment_result===0" @click="onClickClearAlarmBtn(scope.row)" size="mini" title="点击将异常转为已处理状态">标记处理</el-button>
          <i class="el-icon-check" v-if="scope.row.treatment_result===1"  style="color: green">已处理</i>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
        width="60"
        fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="添加维护记录" placement="top">
          <el-button @click="onclickDialogAddMaintainRecord(scope.row)" type="primary" size="mini"><i class="el-icon-plus"></i></el-button>
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>
    <div id="dialog">
      <el-dialog
        :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
        :title="this.isEditRecordOrAddRecord === false ? '添加维护信息' : '修改维护信息'"
        :visible.sync="dialogAddMaintainRecord"
        width="30%"
        :before-close="handleClose">
        <el-form :model="ruleForm" ref="ruleForm" :rules="rules" label-width="100px" class="demo-ruleForm" status-icon>
          <el-form-item label="维护类型" prop="maintain_type">
            <el-select v-model="ruleForm.maintain_type" placeholder="请选择维护类型" style="width: 100%;">
              <el-option label="现场维护" :value="0"></el-option>
              <el-option label="社区维护" :value="1"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="维护电话" prop="phone">
            <el-input v-model="ruleForm.phone"></el-input>
          </el-form-item>
          <el-form-item label="维护人员" prop="maintainer">
            <el-input v-model="ruleForm.maintainer"></el-input>
          </el-form-item>
          <el-form-item label="维护建议" prop="maintain_suggest">
            <el-input type="textarea" v-model="ruleForm.maintain_suggest"></el-input>
          </el-form-item>
          <el-form-item label="维护详情" prop="maintain_message">
            <el-input type="textarea" v-model="ruleForm.maintain_message"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmitForm('ruleForm')">确认</el-button>
            <el-button @click="dialogAddMaintainRecord = false">取消</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
    </div>
    <div id="uploadImg" >
      <el-dialog
        :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
        title="上传图片(一次性最多上传1张)"
        :visible.sync="dialogUploadImgVisible"
        width="30%">
        <el-upload
          class="avatar-uploader"
          ref="upload"
          :action="apiBasicUrl+'/maintain-record/upload'"
          list-type="picture-card"
          :on-remove="handleRemove"
          :on-change="onUploadImg"
          :headers="headersAuth"
          :data="appendMessage"
          :limit="1"
          :auto-upload="false">
          <i class="el-icon-plus"></i>
        </el-upload>
        <el-button style="margin-left: 10px;" size="small" type="success" @click="submitUpload">点击上传</el-button>
      </el-dialog>
    </div>
    <div id="bigImg">
      <el-dialog
        :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
        :visible.sync="isBigImgShow">
        <img width="100%" :src="dialogBigImageUrl" alt="">
      </el-dialog>
    </div>
    <div id="uploadWorldExcel" >
      <el-dialog
        :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
        title="上传Word文档或者Excel文件"
        :visible.sync="dialogUploadWorldExcelVisible"
        width="30%">
        <el-upload
          class="upload-demo"
          ref="uploadFile"
          :action="apiBasicUrl+'/maintain-record/upload'"
          :on-remove="handleRemove"
          :on-change="onUploadWorldExcel"
          :headers="headersAuth"
          :data="appendMessage"
          :file-list="fileList"
          :limit="1"
          :auto-upload="false">
          <el-button slot="trigger" size="small" type="primary">选取文件</el-button>
          <el-button style="margin-left: 10px;" size="small" type="success" @click="submitUploadFile">点击上传</el-button>
        </el-upload>
      </el-dialog>
    </div>
  </div>
</template>

<script>
  import { selectedAddressToParams } from '../../utils/AMap'
  import { _join3 } from '../../utils/string'
  import { getDeviceExceptions, clearAlarm } from '../../api/exceptionReportNew'
  import { getDevicesTotalCount } from '../../api/device'
  import { validatPhones } from '../../utils/validate'
  import { replyRes } from '../../utils/res'
  import { createMaintainRecord, updateMaintainRecord, getMaintainRecord, deleteUploadImgMaintainRecord } from '../../api/maintainRecord'

  const defaultRuleForm = {
    device_exception_id: null,
    maintainer: '',
    phone: null,
    maintain_type: null,
    maintain_message: '',
    maintain_suggest: '',
    uuid: '',
    imgUrl: '',
    imgUrl1: '',
    imgUrl2: ''
  }
  export default {
    name: 'alarmHandle',
    data() {
      return {
        exceptionList: [],
        dialogAddMaintainRecord: false, // 打开修改和添加表单
        isEditRecordOrAddRecord: false, // 判断添加记录还是修改记录
        dialogUploadImgVisible: false, // 打开图片上传窗口
        dialogBigImageUrl: false, // 大图的路径
        isBigImgShow: false, // 是否显示大图
        isShowOrHidden: true, // 是否展开全部表格
        dialogUploadWorldExcelVisible: false, // 上传文件的窗口
        selectedAddress: [],
        appendMessage: { id: null }, // 上传文件的额外参数
        searchForm: {
          expand: 'device,user,maintainRecord',
          treatment_result: null,
          type: null,
          username: ''
        },
        rowList: [],
        testVisible: false,
        permissionName: this.$store.getters.roles[0], // 获取用户的权限分组
        fileList: [], // 文件上传列表
        ruleForm: Object.assign({}, defaultRuleForm),
        // 页码信息
        headersAuth: { 'Authorization': 'Bearer ' + this.$store.getters.token }, // header信息,用于传递toke
        apiBasicUrl: process.env.BASE_API + '/',
        pageInfo: {
          pageSize: 10,
          page: 1,
          total: 0
        },
        totalCount: {
          device: 0,
          alarms: 0,
          unWork: 0
        },
        rules: {
          phone: [
            { required: true, message: '请输入电话号码', trigger: 'blur' },
            { validator: validatPhones(), trigger: 'blur', message: '请输入格式正确的手机号' }
          ],
          maintainer: [
            { required: true, message: '请您填写维护人员', trigger: 'blur' }
          ],
          maintain_suggest: [
            { required: true, message: '请填写维护建议', trigger: 'blur' }
          ],
          maintain_message: [
            { required: true, message: '请填写维护详情', trigger: 'blur' }
          ]
        }
      }
    },
    filters: {
      _join3(value) {
        return _join3(value)
      },
      get_last_two_district_in_a_json_address(json_address) {
        const addArr = JSON.parse(json_address)
        return (addArr.length > 1 ? addArr[addArr.length - 2] : '') + addArr[addArr.length - 1]
      },
      parseFile: function(value) {
        const str = value.split('/')
        return str.pop()
      }
    },
    computed: {
      // 记得一定要用函数的形式
      alarm_type() {
        return this.$store.state.app.alarm_type
      },
      alarm_cause() {
        return this.$store.state.app.alarm_cause
      },
      default_address() {
        return this.$store.state.user.default_address
      },
      finalSearchForm() {
        return Object.assign({}, this.searchForm, this.pageInfo, selectedAddressToParams(JSON.parse(this.default_address)))
      },
      expand_row_keys() {
        return this.exceptionList.filter(row => row.maintainRecord.length).map(({ id }) => id)
      }
    },
    mounted() {
      this.init()
    },
    destroyed() {
      console.clear()
    },
    methods: {
      init() {
        const addressParams = selectedAddressToParams(JSON.parse(this.default_address))
        getDevicesTotalCount(Object.assign({}, addressParams, { state: 2 })).then(res => {
          this.totalCount.unWork = res
        })
        getDevicesTotalCount(Object.assign({}, addressParams, { state: 1 })).then(res => {
          this.totalCount.alarms = res
        })
        getDevicesTotalCount(Object.assign({}, addressParams)).then(res => {
          this.totalCount.device = res
        })
        this.fetchExceptionReports()
      },
      // 获取最新报警信息
      fetchExceptionReports() {
        console.log('fetchExceptionReports>>this.finalSearchForm:', this.finalSearchForm)
        getDeviceExceptions(this.finalSearchForm).then(res => {
          this.exceptionList = res._items
          this.pageInfo.total = res._meta.totalCount
          return true
        })
      },
      /** 获取一条维护记录 */
      fetchMaintainRecord(id) {
        return getMaintainRecord({ id: id }).then(res => {
          this.ruleForm = Object.assign({}, res)
        })
      },
      onSubmitSearchForm() {
        this.fetchExceptionReports()
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchExceptionReports()
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.page = pageNum
        this.fetchExceptionReports()
      },
      /** 点击放大图片 */
      handleBigImg(imgUrl) {
        this.dialogBigImageUrl = this.apiBasicUrl + imgUrl
        this.isBigImgShow = true
      },
      /** 关闭表格时清除表单 */
      handleClose() {
        this.dialogAddMaintainRecord = false
        this.ruleForm = Object.assign({}, defaultRuleForm)
      },
      /** 移除上传文件列表 */
      handleRemove(file, fileList) {
        console.log('__', fileList)
      },
      /** 添加维护记录按钮 */
      onclickDialogAddMaintainRecord(item) {
        this.ruleForm.device_exception_id = item.id
        this.ruleForm.uuid = item.uuid
        this.dialogAddMaintainRecord = true
        this.isEditRecordOrAddRecord = false
      },
      /** 编辑按钮 */
      onclickDialogEditMaintainRecord(item) {
        this.dialogAddMaintainRecord = true
        this.isEditRecordOrAddRecord = true
        this.fetchMaintainRecord(item.id)
      },
      /** 删除图片 */
      onDialogDeleteImg(obj) {
        this.$confirm('此操作将永久删除该文件或图片, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
          center: true
        }).then(() => {
          deleteUploadImgMaintainRecord(obj).then(res => {
            if (replyRes(res)) {
              this.fetchExceptionReports()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          })
        })
      },
      /** 点击打开上传图片窗口 */
      onDialogUploadImg(item) {
        this.appendMessage = Object.assign(this.appendMessage, item)
        this.dialogUploadImgVisible = true
      },
      /** 上传world文档或者excel表格*/
      onDialogUploadWordOrExcel(item) {
        this.dialogUploadWorldExcelVisible = true
        this.appendMessage = Object.assign(this.appendMessage, item)
      },
      /** 添加维护记录和修改维护记录 */
      onSubmitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            if (this.isEditRecordOrAddRecord === false) {
              createMaintainRecord(this.ruleForm).then(res => {
                this.dialogAddMaintainRecord = false
                if (replyRes(res)) {
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchExceptionReports()
                }
              })
            } else {
              updateMaintainRecord(this.ruleForm).then(res => {
                this.dialogAddMaintainRecord = false
                if (replyRes(res)) {
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchExceptionReports()
                }
              })
            }
          } else {
            return false
          }
        })
      },
      /** 上传文件成功或者失败后执行的钩子 */
      onUploadWorldExcel(file, fileList) {
        if (file.response) {
          this.$refs.uploadFile.clearFiles() // 清空已上传的文件列表
          this.appendMessage = Object.assign({}, { id: null }) // 清除上传缓存数据
          if (replyRes(file.response)) {
            this.fetchExceptionReports()
            this.dialogUploadWorldExcelVisible = false
          }
        }
      },
      /** 上传图片成功或者失败后执行的钩子 */
      onUploadImg(file, fileList) {
        if (file.response) {
          this.$refs.upload.clearFiles() // 清空已上传的文件列表
          this.appendMessage = Object.assign({}, { id: null }) // 清除上传缓存数据
          if (replyRes(file.response)) {
            this.fetchExceptionReports()
            this.dialogUploadImgVisible = false
          }
        }
      },
      /** 上传图片 */
      submitUpload() {
        this.$refs.upload.submit()
      },
      /** 上传文件 */
      submitUploadFile() {
        this.$refs.uploadFile.submit()
      },
      parseType(row) {
        const type = row.maintain_type === 0 ? '现场维护' : '社区维护'
        return type
      },
      rowKey(row) {
        return row.id
      },
      /** 切换子表格展开还是关闭 */
      onchangeShowOrHidden() {
        this.isShowOrHidden = !this.isShowOrHidden
      },
      onClickClearAlarmBtn(item) {
        this.$prompt('请输入密码', '确定清除【设备id' + item.uuid + '】 的异常状态？是请输入1', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /1/,
          inputErrorMessage: '密码错误'
        }).then(({ value }) => {
          clearAlarm({ uuid: item.uuid, id: item.id }).then(res => {
            replyRes(res)
            this.fetchExceptionReports()
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '取消操作'
          })
        })
      }
    }
  }
</script>
<style scoped>
  .app-container-alarmManage {}
  .box-shadow-deep{box-shadow: 3px 3px 5px #888;}
  .title-tr td{color: white}
  .app-container-row-02{display: flex}
  .app-container-row-03{display: flex;justify-content:space-between;width: 100%}

  .app-container-row-02 .summary-box{width: 25%;min-width: 200px;padding: 15px;display: flex;line-height: 80px;font-size: 20px;text-align: center;font-weight: bolder}
  .app-container-row-02 .summary-box .left{width: 40%;color: white;border-radius: 10px 0 0 10px;}
  .app-container-row-02 .summary-box .right{width: 60%;background: white;}
  .tooltip{position: relative;}
  .img:hover + .el-icon-delete{opacity: 0.7;}
  .el-icon-delete{width: 100%;height: 20px;background: black; text-align: center;line-height: 20px;position: absolute;opacity:0;bottom: 6px;}
  .el-icon-delete:hover {opacity: 0.7;}
  p{margin: 0}


</style>
