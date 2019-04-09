<template>
  <div class="app-container">
    <div style="margin: 10px 0">
      <!--筛选表单-->
      <el-form :inline="true" :model="searchForm">
        <el-form-item label="imei">
          <el-input v-model="searchForm.imei" placeholder="请输入用户名"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onClickSearchBtn">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div>
      <p style="color: green">友情提示:请点击对应参数进行修改,注意反馈下面如果是未确认,将不能修改.因为在此之前有信息修改还未反馈</p>
    </div>
    <!--用户组列表-->
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
    <table class="_table">
      <tr>
        <th>ID</th>
        <th>IMEI</th>
        <th>PWD</th>
        <th>PT</th>
        <th>CT</th>
        <th>V</th>
        <th>E</th>
        <th>LC</th>
        <th>T</th>
        <th>CEF</th>
        <th>EPEM</th>
        <th>AD</th>
        <th>RTTRC</th>
        <th>CHRP</th>
        <th>HC</th>
        <th>HCUEFL</th>
        <th>HCUEFH</th>
        <th>更新时间</th>
        <th>反馈</th>
        <th>操作</th>
      </tr>
      <tr v-for="item in list" :key="item.id">
        <td>{{item.id}}</td>
        <td>{{item.imei}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,pwd:item.pwd}, key: 'pwd'})">{{item.pwd}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,pt:item.pt}, key: 'pt'})">{{item.pt}}</td>
        <td>
          ct1<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,ct1:item.ct1}, key: 'ct1'})">{{item.ct1}}</el-button><br>
          ct2<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,ct2:item.ct2}, key: 'ct2'})">{{item.ct2}}</el-button><br>
          ct3<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,ct3:item.ct3}, key: 'ct3'})">{{item.ct3}}</el-button><br>
          ct4<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,ct4:item.ct4}, key: 'ct4'})">{{item.ct4}}</el-button>
        </td>
        <td>
          v1<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,v1:item.v1}, key: 'v1'})">{{item.v1}}</el-button><br>
          v2<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,v2:item.v2}, key: 'v2'})">{{item.v2}}</el-button><br>
          v3<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,v3:item.v3}, key: 'v3'})">{{item.v3}}</el-button><br>
          v4<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,v4:item.v4}, key: 'v4'})">{{item.v4}}</el-button>
        </td>
        <td>
          e1<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,e1:item.e1}, key: 'e1'})">{{item.e1}}</el-button><br>
          e2<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,e2:item.e2}, key: 'e2'})">{{item.e2}}</el-button><br>
          e3<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,e3:item.e3}, key: 'e3'})">{{item.e3}}</el-button><br>
          e4<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,e4:item.e4}, key: 'e4'})">{{item.e4}}</el-button>
        </td>
        <td>
          lc1<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,lc1:item.lc1}, key: 'lc1'})">{{item.lc1}}</el-button><br>
          lc2<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,lc2:item.lc2}, key: 'lc2'})">{{item.lc2}}</el-button><br>
          lc3<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,lc3:item.lc3}, key: 'lc3'})">{{item.lc3}}</el-button><br>
          lc4<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,lc4:item.lc4}, key: 'lc4'})">{{item.lc4}}</el-button>
        </td>
        <td>
          t1<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,t1:item.t1}, key: 't1'})">{{item.t1}}</el-button><br>
          t2<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,t2:item.t2}, key: 't2'})">{{item.t2}}</el-button><br>
          t3<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,t3:item.t3}, key: 't3'})">{{item.t3}}</el-button><br>
          t4<el-button size="mini" @click="onChangeEdit({params:{imei:item.imei,t4:item.t4}, key: 't4'})">{{item.t4}}</el-button>
        </td>
        <td @click="onChangeEdit({params:{imei:item.imei,cef:item.cef}, key: 'cef' })">{{item.cef}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,epem:item.epem},  key: 'epem' })">{{item.epem}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,ad:item.ad},  key: 'ad' })">{{item.ad}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,rttrc:item.rttrc},  key: 'rttrc' })">{{item.rttrc}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,chrp:item.chrp}, key: 'chrp' })">{{item.chrp}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,hc:item.hc}, key: 'hc' })">{{item.hc}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,hcuefl:item.hcuefl}, key: 'hcuefl' })">{{item.hcuefl}}</td>
        <td @click="onChangeEdit({params:{imei:item.imei,hcuefh:item.hcuefh}, key: 'hcuefh' })">{{item.hcuefh}}</td>
        <td>{{item.update_time}}</td>
        <td>{{item.verified === 1? '已确认' : '未确认'}}</td>
        <td>
          <el-button size="mini" @click="onchangeFlush" style="float: left">刷新</el-button>
          <el-button size="mini" @click="onClickEdit(item)" type="primary" icon="el-icon-edit" style="float: left;margin: 0">修改</el-button>
        </td>
      </tr>
    </table>
    <el-dialog
      title="设备参数修改"
      :visible.sync="visible.ruleForm"
      width="30%">
      <el-form :model="ruleForm" ref="ruleForm" :rules="rules" label-width="100px" status-icon>
        <el-row :gutter="20">
          <el-col :span="10">
            <el-form-item label="ct1" prop="ct1">
              <el-input v-model.number="ruleForm.ct1"></el-input>
            </el-form-item>
            <el-form-item label="ct2" prop="ct2">
              <el-input v-model.number="ruleForm.ct2"></el-input>
            </el-form-item>
            <el-form-item label="ct3" prop="ct3">
              <el-input v-model.number="ruleForm.ct3"></el-input>
            </el-form-item>
            <el-form-item label="ct4" prop="ct4">
              <el-input v-model.number="ruleForm.ct4"></el-input>
            </el-form-item>
            <el-form-item label="v1" prop="v1">
              <el-input v-model.number="ruleForm.v1"></el-input>
            </el-form-item>
            <el-form-item label="v2" prop="v2">
              <el-input v-model.number="ruleForm.v2"></el-input>
            </el-form-item>
            <el-form-item label="v3" prop="v3">
              <el-input v-model.number="ruleForm.v3"></el-input>
            </el-form-item>
            <el-form-item label="v4" prop="v4">
              <el-input v-model.number="ruleForm.v4"></el-input>
            </el-form-item>
            <el-form-item label="e1" prop="e1">
              <el-input v-model.number="ruleForm.e1"></el-input>
            </el-form-item>
            <el-form-item label="e2" prop="e2">
              <el-input v-model.number="ruleForm.e2"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="e3" prop="e3">
              <el-input v-model.number="ruleForm.e3"></el-input>
            </el-form-item>
            <el-form-item label="e4" prop="e4">
              <el-input v-model.number="ruleForm.e4"></el-input>
            </el-form-item>
            <el-form-item label="lc1" prop="lc1">
              <el-input v-model.number="ruleForm.lc1"></el-input>
            </el-form-item>
            <el-form-item label="lc2" prop="lc2">
              <el-input v-model.number="ruleForm.lc2"></el-input>
            </el-form-item>
            <el-form-item label="lc3" prop="lc3">
              <el-input v-model.number="ruleForm.lc3"></el-input>
            </el-form-item>
            <el-form-item label="lc4" prop="lc4">
              <el-input v-model.number="ruleForm.lc4"></el-input>
            </el-form-item>
            <el-form-item label="t1" prop="t1">
              <el-input v-model.number="ruleForm.t1"></el-input>
            </el-form-item>
            <el-form-item label="t2" prop="t2">
              <el-input v-model.number="ruleForm.t2"></el-input>
            </el-form-item>
            <el-form-item label="t3" prop="t3">
              <el-input v-model.number="ruleForm.t3"></el-input>
            </el-form-item>
            <el-form-item label="t4" prop="t4">
              <el-input v-model.number="ruleForm.t4"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="submitForm('ruleForm')" type="primary">提 交</el-button>
        <el-button @click="visible.ruleForm = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import { getDeviceParameterSettings, sendUpdateInstruction, getDeviceParameterSetting } from '../../api/deviceSetting'
import { replyRes } from '../../utils/res'
const defaultRuleform = {
  id: null,
  ct1: null, ct2: null, ct3: null, ct4: null,
  v1: null, v2: null, v3: null, v4: null,
  e1: null, e2: null, e3: null, e4: null,
  lc1: null, lc2: null, lc3: null, lc4: null,
  t1: null, t2: null, t3: null, t4: null
}
export default {
  data() {
    return {
      list: [],
      visible: {
        ruleForm: false
      },
      searchForm: {
        imei: null
      },
      pageInfo: {
        pageSize: 10,
        page: 1,
        total: 0
      },
      rules: {
        ct1: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        ct2: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        ct3: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        ct4: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        v1: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        v2: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        v3: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        v4: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        e1: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        e2: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        e3: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        e4: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        lc1: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        lc2: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        lc3: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        lc4: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        t1: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        t2: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        t3: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ],
        t4: [
          { type: 'number', required: true, message: '请输入数值', trigger: 'blur' }
        ]
      },
      message: {},
      ruleForm: Object.assign({}, defaultRuleform)
    }
  },
  computed: {
    finalSearchForm() {
      this.searchForm = Object.assign({}, this.searchForm, this.pageInfo)
      return this.searchForm
    }
  },
  methods: {
    init() {
      this.fetchList()
    },
    fetchList() {
      getDeviceParameterSettings(this.finalSearchForm).then(res => {
        this.list = res._items
        this.pageInfo.total = res._meta.totalCount
      })
    },
    /** 根据imei获取一条记录 */
    fetchDeviceParameterSetting(imei) {
      getDeviceParameterSetting({ imei: imei }).then(res => {
        this.ruleForm = res
      })
    },
    onChangeEdit(obj) {
      this.$prompt('请输入', '提示', {
        cancelButtonText: '取消',
        inputPattern: /^[0-9]*$/,
        inputValue: obj.params[obj.key],
        inputErrorMessage: '只能输入数字'
      }).then(({ value }) => {
        obj.params[obj.key] = value
        sendUpdateInstruction(obj.params).then(res => {
          if (replyRes(res)) {
            this.fetchList()
          }
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '取消输入'
        })
      })
    },
    handleSizeChange(size) {
      console.log('change page size', size)
      this.pageInfo.pageSize = size
      this.fetchList()
    },
    handleCurrentChange(pageNum) {
      console.log('change pageNum', pageNum)
      this.pageInfo.page = pageNum
      this.fetchList()
    },
    onClickSearchBtn() {
      this.fetchList()
    },
    /** 修改按钮 */
    onClickEdit(item) {
      this.visible.ruleForm = true
      this.fetchDeviceParameterSetting(item.imei)
    },
    onchangeFlush() {
      this.fetchList()
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          sendUpdateInstruction(this.ruleForm).then(res => {
            if (replyRes(res)) {
              this.fetchList()
              this.visible.ruleForm = false
            }
          })
        } else {
          return false
        }
      })
    }
  },
  mounted() {
    this.init()
  }
}
</script>
