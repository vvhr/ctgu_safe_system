<template>
  <div class="app-container-user">
    <!--顶部操作按钮-->
    <div style="margin: 10px 0">
      <!--筛选表单-->
      <el-form :inline="true" :model="searchForm">
        <el-form-item label="用户名">
          <el-input size="small" v-model="searchForm.username" placeholder="请输入用户名"></el-input>
        </el-form-item>
        <el-form-item label="电话">
          <el-input size="small" v-model="searchForm.phone" placeholder="请输入用户名"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" @click="onClickSearchBtn">查询</el-button>
        </el-form-item>
        <el-form-item>
          <!--新增按钮-->
          <el-button size="small" @click = "onClickCreateBtn"><i class="el-icon-circle-plus-outline"></i> 新增</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--创建表单-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      :title="ruleForm.id===null?'创建用户':'编辑用户'" :visible.sync="visible.ruleForm">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
        <el-form-item label="用户组" prop="user_group_id">
          <el-input v-model.number="ruleForm.user_group_id"></el-input>
        </el-form-item>
        <el-form-item label="所属项目" prop="project_id">
          <el-input v-model.number="ruleForm.project_id"></el-input>
        </el-form-item>
        <el-form-item label="用户名" prop="username">
          <el-input v-model="ruleForm.username"></el-input>
        </el-form-item>
        <el-form-item label="真实姓名" prop="realname">
          <el-input v-model="ruleForm.realname"></el-input>
        </el-form-item>
        <el-form-item label="电话" prop="phone">
          <el-input v-model="ruleForm.phone"></el-input>
        </el-form-item>
        <el-form-item label="用户密码" prop="password" aria-placeholder="不修改密码则不必填写">
          <el-input type="password" v-model="ruleForm.password"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="mini" type="primary" @click="submitForm('ruleForm')">提交</el-button>
          <el-button size="mini" @click="visible.ruleForm = false">关闭</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!--分页区-->
    <div class="block">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pageInfo.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="5"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pageInfo.totalCount">
      </el-pagination>
    </div>
    <!--list列表区-->
    <el-table height="550" :data="list" stripe border fit row-key="id">
      <!--<el-table-column label="id" prop="id" width="50"></el-table-column>-->
      <el-table-column label="用户名" prop="username" width="300"></el-table-column>
      <el-table-column label="真实姓名" prop="realname" width="300"></el-table-column>
      <el-table-column label="电话" prop="phone" width="200"></el-table-column>
      <el-table-column label="用户组" prop="user_group_id"></el-table-column>
      <el-table-column label="操作" width="300">
        <template slot-scope="scope">
          <el-button-group>
            <el-button size="mini" type="danger" @click = "onClickModifyBtn(scope.row)"><i class="el-icon-edit"></i> 修改</el-button>
            <el-button size="mini" type="primary" v-if="scope.row.user_group_id === 3" @click = "onClickShowUserDeviceBtn(scope.row)"><i class="el-icon-delete"></i> 关联设备</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <!--用户关联设备对话框-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      :title="'当前用户 >> [' + activeUser.username + '] 已绑定的设备列表'" :visible.sync="visible.userDevice" width="70%">
      <userDevice :user="activeUser"></userDevice>
    </el-dialog>
  </div>
</template>

<script>
  import { getUser, getUsers, createUser, updateUser } from '../../api/user'
  import { replyRes } from '../../utils/res'
  import { enableOrNotReceiveMsg } from '../../api/wxUser'
  import userDevice from './projectDevice/userDevice'

  const defaultRuleForm = {
    id: null,
    username: '',
    realname: '',
    phone: '',
    password: '',
    user_group_id: 3,
    project_id: 1
  }
  export default {
    components: { userDevice },
    data() {
      return {
        activeUser: {},
        searchForm: {
          username: '',
          phone: '',
          expand: 'wxUsers'
        },
        // 用户列表
        list: [],
        // 页码信息
        pageInfo: {
          pageSize: 10,
          page: 1,
          totalCount: 0
        },
        visible: {
          ruleForm: false,
          userDevice: false
        },
        ruleForm: Object.assign({}, defaultRuleForm),
        rules: {
          username: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          realname: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          // password: [
          //   { required: true, message: '请输入', trigger: 'blur' },
          //   { min: 6, message: '长度在必须大于6', trigger: 'blur' }
          // ],
          phone: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 11, max: 11, message: '长度在必须大于11', trigger: 'blur' }
          ],
          user_group_id: [
            { type: 'number', required: true, message: '请输入', trigger: 'blur' }
          ],
          project_id: [
            { type: 'number', required: true, message: '请输入', trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      finalSearchForm() {
        return Object.assign(this.searchForm, this.pageInfo)
      }
    },
    mounted() {
      this.fetchList()
    },
    methods: {
      fetchList() {
        console.log(this.finalSearchForm)
        getUsers(this.finalSearchForm).then(res => {
          // console.log('fetchList:res', res)
          this.list = res._items
          this.pageInfo.totalCount = res._meta.totalCount
        })
      },
      onClickCreateBtn() {
        this.ruleForm = Object.assign({}, defaultRuleForm)
        this.visible.ruleForm = true
      },
      onClickModifyBtn(item) {
        getUser(item).then(res => {
          this.ruleForm = Object.assign({}, defaultRuleForm, res)
          console.log('点击修改用户信息时', this.ruleForm)
          this.visible.ruleForm = true
        })
      },
      onClickShowUserDeviceBtn(item) {
        this.activeUser = item
        this.visible.userDevice = true
      },
      onClickSearchBtn() {
        this.fetchList()
      },
      onSwitchEnableReceiveMsg(wxUser) {
        enableOrNotReceiveMsg({ id: wxUser.id }).then(res => {
          if (replyRes(res)) {
            wxUser.enable_receive_msg = res.bData.enable_receive_msg
          }
        })
      },
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            if (this.ruleForm.id === null) {
              createUser(this.ruleForm).then(res => {
                if (replyRes(res)) {
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchList(this.finalSearchForm)
                  this.visible.ruleForm = false
                }
              })
            } else {
              updateUser(this.ruleForm).then(res => {
                if (replyRes(res)) {
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchList(this.finalSearchForm)
                  this.visible.ruleForm = false
                }
              })
            }
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },
      handleSizeChange(size) {
        this.pageInfo.pageSize = size
        this.fetchList()
      },
      handleCurrentChange(page) {
        this.pageInfo.page = page
        this.fetchList()
      }
    }
  }
</script>

<style scoped>
  .app-container-user{
    padding: 10px;
  }
</style>
