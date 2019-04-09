<template>
  <div class="app-container-project">
    <div>
      <el-form :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="项目名称">
          <el-input size="small" v-model="searchForm.project_name" placeholder="项目名称"></el-input>
        </el-form-item>
        <el-form-item label="项目地址">
          <el-input size="small"v-model="searchForm.address" placeholder="项目地址"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" @click="onSubmitSearch">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--顶部操作按钮-->
    <div>
      <el-button size="small" style="width: 150px" type="primary" @click = "onClickCreateBtn"><i class="el-icon-circle-plus-outline"></i> 新增</el-button>
    </div>
    <!--创建表单-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      :title="this.activeId===0?'创建项目':'编辑项目'" :visible.sync="visible.ruleForm">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="地址" prop="address">
              <el-input v-model="ruleForm.address"></el-input>
            </el-form-item>
            <el-form-item label="负责人电话" prop="contact">
              <el-input v-model="ruleForm.contact"></el-input>
            </el-form-item>
            <el-form-item label="维护人电话" prop="contact_m">
              <el-input v-model="ruleForm.contact_m"></el-input>
            </el-form-item>
            <el-form-item label="安装时间" prop="installed_at">
              <el-input v-model="ruleForm.installed_at"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="维护人员" prop="maintenance">
              <el-input v-model="ruleForm.maintenance"></el-input>
            </el-form-item>
            <el-form-item label="负责人" prop="person_liable">
              <el-input v-model="ruleForm.person_liable"></el-input>
            </el-form-item>
            <el-form-item label="项目名称" prop="project_name">
              <el-input v-model="ruleForm.project_name"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item>
          <el-button size="small" type="primary" @click="submitForm('ruleForm')">提交</el-button>
          <el-button size="small" @click="visible.ruleForm = false">关闭</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!--分页区-->
    <div class="block">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pageInfo.pageNum"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="5"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pageInfo.total">
      </el-pagination>
    </div>
    <!--list项目列表区-->
    <el-table height="550" :data="list" stripe border fit row-key="id">
      <el-table-column label="id" prop="id" width="50" align="center"></el-table-column>
      <el-table-column label="项目名称" prop="project_name" width="200"  align="center"></el-table-column>
      <el-table-column label="项目地址" prop="address" align="center"></el-table-column>
      <el-table-column label="项目负责人" prop="id" width="200" align="center">
        <template slot-scope="scope">
          {{scope.row.person_liable + ' : ' + scope.row.contact}}
        </template>
      </el-table-column>
      <el-table-column label="维护人员" prop="id" width="200" align="center">
        <template slot-scope="scope">
          {{scope.row.maintenance + ' : ' + scope.row.contact_m}}
        </template>
      </el-table-column>
      <el-table-column label="操作" prop="id" width="290">
        <template slot-scope="scope">
          <el-button type="warning" @click = "onClickModifyBtn(scope.row.id)" size="mini"><i class="el-icon-edit"></i> 修改</el-button>
          <el-button type="danger" @click = "onClickDelBtn(scope.row)" size="mini"><i class="el-icon-delete"></i> 禁用</el-button>
          <el-button type="primary" @click = "onClickBindDeviceBtn(scope.row)" size="mini"><i class="el-icon-menu"></i> 绑定设备</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!--项目关联设备对话框-->
    <el-dialog
      :modal-append-to-body="true" :modal="true" :lock-scroll="true" :append-to-body="true"
      :title="'项目 >> [' + activeProject.project_name + '] 已绑定的设备列表'" :visible.sync="visible.projectDevice" width="70%">
      <projectDevice :project="activeProject"></projectDevice>
    </el-dialog>
  </div>
</template>
<script>
  import { getProjects, getProject, createProject, updateProject } from '../../api/project'
  import { replyRes } from '../../utils/res'
  import projectDevice from './projectDevice/projectDevice'
  // import _ from 'lodash'

  const defaultRuleForm = {
    // 'id': 0
    'address': '湖北省宜昌市西陵区西陵街道XX路X苑学生公寓',
    'contact': '13888888888',
    'contact_m': '13888888888',
    'installed_at': '2018-11-26 09:35:08',
    'maintenance': '刘保安',
    'person_liable': '张保安',
    'project_name': '三峡大学X苑用电安全监控系统',
    'project_type': 1,
    'term': 1
  }
  const defaultSearchForm = {
    start_time: null,
    end_time: null,
    project_name: '',
    address: '',
    region_ids: null
  }
  export default {
    data() {
      return {
        activeProject: {},
        activeId: 0,
        // 对话框可见性
        visible: {
          ruleForm: false,
          projectDevice: false
        },
        // 项目信息列表
        list: [],
        // 页码信息
        pageInfo: {
          size: 10,
          pageNum: 1,
          total: 0
        },
        // 查询表单
        searchForm: Object.assign({}, defaultSearchForm),
        ruleForm: Object.assign({}, defaultRuleForm),
        rules: {
          address: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          contact: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 10, message: '长度在必须是11位', trigger: 'blur' }
          ],
          contact_m: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 10, message: '长度在必须是11位', trigger: 'blur' }
          ],
          installed_at: [
            { required: true, message: '请输入', trigger: 'blur' }
          ],
          maintenance: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          person_liable: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ],
          project_name: [
            { required: true, message: '请输入', trigger: 'blur' },
            { min: 2, message: '长度在必须大于2', trigger: 'blur' }
          ]
        }
      }
    },
    components: {
      projectDevice
    },
    computed: {
      // 最终发送到getProjects接口的查询参数
      finalSearchForm: function() {
        const tempObj = {}
        for (const key in this.searchForm) {
          if (this.searchForm[key] !== null) {
            tempObj[key] = this.searchForm[key]
          }
        }
        const finalSearchForm = Object.assign(
          tempObj,
          {
            page: this.pageInfo.pageNum,
            size: this.pageInfo.size
          },
        )
        console.log('finalSearchForm', finalSearchForm)
        return finalSearchForm
      }
    },
    mounted() {
      this.fetchProjects(this.finalSearchForm)
    },
    methods: {
      /** 调用接口获取数据改变状态方法类 **/
      fetchProjects() {
        getProjects(this.finalSearchForm).then(data => {
          this.list = data._items
          this.pageInfo.total = data._meta.totalCount
        })
      },
      fetchProject() {
        return getProject({ id: this.activeId }).then(data => {
          console.log('active project', data)
          this.ruleForm = data
        })
      },
      handleSizeChange(size) {
        this.pageInfo.size = size
        this.fetchProjects()
      },
      onClickDelBtn(item) {
        this.$confirm('确定是否禁用项目, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          updateProject({ id: item.id, is_del: 1 }).then(res => {
            if (replyRes(res)) {
              this.fetchProjects()
            }
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          })
        })
      },
      handleCurrentChange(pageNum) {
        this.pageInfo.pageNum = pageNum
        this.fetchProjects()
      },
      /** 事件类方法 **/
      onClickCreateBtn() {
        console.log('onClickCreateBtn')
        this.visible.ruleForm = true
      },
      onClickModifyBtn(id) {
        console.log('onClickModifyBtn', id)
        this.activeId = id
        this.fetchProject()
        this.visible.ruleForm = true
      },
      onClickBindDeviceBtn(project) {
        this.activeProject = project
        this.visible.projectDevice = true
      },
      onSubmitSearch() {
        console.log('finalSearchForm', this.finalSearchForm)
        this.fetchProjects()
      },
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            if (this.activeId === 0) {
              createProject(this.ruleForm).then(res => {
                if (replyRes(res)) {
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchProjects(this.finalSearchForm)
                  this.visible.ruleForm = false
                }
              })
            } else {
              console.log('update project', this.ruleForm)
              const data = Object.assign({ id: this.activeId }, this.ruleForm)
              updateProject(data).then(res => {
                if (replyRes(res)) {
                  this.activeId = 0
                  this.ruleForm = Object.assign({}, defaultRuleForm)
                  this.fetchProjects(this.finalSearchForm)
                  this.visible.ruleForm = false
                }
              })
            }
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    }
  }
</script>
<style scoped>
  .app-container-project {}
</style>
