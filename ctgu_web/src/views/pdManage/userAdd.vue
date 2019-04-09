<template>
  <div class="app-container">
    <!--创建表单-->
    <el-dialog title="添加用户" :visible.sync="visible.dispatchForm">
      <el-form :model="userInfo" ref="userInfo" :rules="rules" label-width="100px">
        <el-row>
          <el-col :span="12">
            <el-form-item label="用户名" prop="username">
              <el-input v-model="userInfo.username" class="el-input"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
              <el-input v-model="userInfo.password"></el-input>
            </el-form-item>
            <el-form-item label="电话" prop="phone">
              <el-input v-model="userInfo.phone"></el-input>
            </el-form-item>
            <el-form-item label="身份证号" prop="idNumber">
              <el-input v-model="userInfo.idNumber"></el-input>
            </el-form-item>
           </el-col>
            <el-col :span="12">
            <el-form-item label="项目ID" prop="projectId">
              <el-input v-model="userInfo.projectId"></el-input>
            </el-form-item>
            <el-form-item label="父类ID" prop="parentId">
              <el-input v-model="userInfo.parentId"></el-input>
            </el-form-item>
            <el-form-item label="状态" prop="state">
              <el-input v-model="userInfo.state"></el-input>
            </el-form-item>
            <el-form-item label="用户类型" prop="userType">
              <el-input v-model="userInfo.userType"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item>
          <el-button @click="onUserForm">提交</el-button>
          <el-button @click="visible.dispatchForm = false">关闭</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!--&lt;!&ndash;顶部操作按钮&ndash;&gt;-->
    <div style="margin: 10px 0">
      <el-button @click="onUserAdd"><i class="el-icon-circle-plus-outline"></i> 新增</el-button>
    </div>
    <!--list项目列表区-->
    <table class="_table">
      <thead>
      <tr>
        <th>id</th>
        <th>用户名</th>
        <th>电话</th>
        <th>身份证号</th>
        <th>状态</th>
        <th>项目ID</th>
        <th>用户类型</th>
        <th>登录时间</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in userList.list" :key="key">
        <td>{{item.id}}</td>
        <td>{{item.username}}</td>
        <td>{{item.phone}}</td>
        <td>{{item.idNumber}}</td>
        <td>{{item.state}}</td>
        <td>{{item.projectId}}</td>
        <td>{{item.userType}}</td>
        <td>{{item.loginTime}}</td>
      </tr>
      </tbody>
    </table>
    <div class="block">
      <el-pagination
        @current-change="onPageChange"
        :page-size="params.size"
        layout="prev, pager, next"
        :total="params.size * userList.totalPageNum">
      </el-pagination>
    </div>
  </div>
</template>

<script>
  import { getUserListPage } from '../../api/login'
  import { insert } from '../../api/login'
  export default {
    data() {
      return {
        // 对话框可见性
        visible: {
          ruleForm: false,
          dispatchForm: false
        },
        userList: {
          list: {},
          totalPageNum: 0
        },
        // 页码信息
        params: {
          size: 10,
          pagenum: 1
        },
        userInfo: {
          idNumber: '',
          parentId: 1,
          password: '',
          phone: '',
          projectId: 1,
          state: 1,
          userType: 1,
          username: ''
        },
        rules: {
          username: [
            { required: true, message: '请输入用户名', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2位', trigger: 'blur' }
          ],
          password: [
            { required: true, message: '请输入密码', trigger: 'blur' },
            { min: 6, message: '长度在必须大于6位', trigger: 'blur' }
          ],
          phone: [
            { required: true, message: '请输入电话号码', trigger: 'blur' },
            { min: 11, message: '长度在必须是11位', trigger: 'blur' }
          ],
          idNumber: [
            { required: true, message: '请输入身份证号', trigger: 'blur' },
            { min: 18, message: '输入类型为整数,长度必须为18位' }
          ]
        }
      }
    },
    mounted() {
      this.fetchUserListPage()
    },
    methods: {
      fetchUserListPage() {
        getUserListPage(this.params).then(res => {
          this.userList = res.data
        })
      },
      // 添加用户
      insertUser() {
        insert(this.userInfo).then(res => {
          console.log(res.msg)
        })
      },
      // 提交新增用户表单
      onUserForm() {
        this.insertUser()
      },
      // 切换分页
      onPageChange(index) {
        this.params.pagenum = index
        this.fetchUserListPage()
      },
      // 打开弹窗页面
      onUserAdd() {
        this.visible.dispatchForm = true
      }
    }
  }
</script>

<style scoped>

</style>
