<template>
  <div class="app-container">
    <!--头部操作按钮-->
    <div>
      <el-button>新增根节点</el-button>
    </div>
    <!--表格区-->
    <div>
      <el-tree
        :data="tree"
        show-checkbox
        default-expand-all
        node-key="id"
        :props="{children: 'children', label: 'menu_name'}">
          <span class="custom-tree-node" slot-scope="{ node, data }">
            <span>{{data.menu_name}}</span>
            <span><el-button type="text" icon="el-icon-edit" size="mini" @click="onClickToEdit(data)">修改</el-button></span>
            <span><el-button type="text" icon="el-icon-delete" size="mini" @click="onClickDeleteMenu(data)">删除</el-button></span>
            <span><el-button type="text" icon="el-icon-circle-plus-outline" size="mini">新增</el-button></span>
          </span>
      </el-tree>
    </div>
    <!--菜单编辑对话框-->
    <!--创建表单-->
    <el-dialog :title="this.ruleForm.id === null?'新增菜单':('编辑菜单 : ' + this.ruleForm.menu_name)" :visible.sync="visible.ruleForm">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="是否有子菜单" prop="address">
              <el-input v-model="ruleForm.address"></el-input>
            </el-form-item>
            <el-form-item label="负责人电话" prop="contact">
              <el-input v-model="ruleForm.contact"></el-input>
            </el-form-item>
            <el-form-item label="维护人电话" prop="contact_m">
              <el-input v-model="ruleForm.contact_m"></el-input>
            </el-form-item>
            <el-form-item label="安装数量" prop="install_num">
              <el-input v-model.number="ruleForm.install_num"></el-input>
            </el-form-item>
            <el-form-item label="安装时间" prop="installed_at">
              <el-input v-model="ruleForm.installed_at"></el-input>
            </el-form-item>
            <el-form-item label="维护人员" prop="maintenance">
              <el-input v-model="ruleForm.maintenance"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="负责人" prop="person_liable">
              <el-input v-model="ruleForm.person_liable"></el-input>
            </el-form-item>
            <el-form-item label="项目名称" prop="project_name">
              <el-input v-model="ruleForm.project_name"></el-input>
            </el-form-item>
            <el-form-item label="项目类型" prop="project_type">
              <el-input v-model="ruleForm.project_type"></el-input>
            </el-form-item>
            <el-form-item label="区域ID" prop="region_id">
              <el-input v-model.number="ruleForm.region_id"></el-input>
            </el-form-item>
            <el-form-item label="合同期限" prop="term">
              <el-input v-model.number="ruleForm.term"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
          <el-button @click="visible.ruleForm = false">关闭</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import { getMenus } from '../../api/menu'
const defaultRuleForm = {
  id: null,
  has_children: null,
  menu_name: null,
  route_name: null
}
export default {
  data() {
    return {
      list: [],
      tree: [],
      ruleForm: Object.assign({}, defaultRuleForm),
      rules: {
        // address: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 2, message: '长度在必须大于2', trigger: 'blur' }
        // ],
        // contact: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 10, message: '长度在必须是11位', trigger: 'blur' }
        // ],
        // contact_m: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 10, message: '长度在必须是11位', trigger: 'blur' }
        // ],
        // install_mum: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { type: 'number', message: '必须为数字值' }
        // ],
        // installed_at: [
        //   { required: true, message: '请输入', trigger: 'blur' }
        // ],
        // maintenance: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 2, message: '长度在必须大于2', trigger: 'blur' }
        // ],
        // person_liable: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 2, message: '长度在必须大于2', trigger: 'blur' }
        // ],
        // project_name: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { min: 2, message: '长度在必须大于2', trigger: 'blur' }
        // ],
        // project_type: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { type: 'number', message: '必须为数字值' }
        // ],
        // region_id: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { type: 'number', message: '必须为数字值' }
        // ],
        // term: [
        //   { required: true, message: '请输入', trigger: 'blur' },
        //   { type: 'number', message: '必须为数字值' }
        // ]
      },
      visible: {
        ruleForm: false
      }
    }
  },
  methods: {
    init() {
      this.fetchMenus()
    },
    fetchMenus() {
      getMenus().then(res => {
        console.log('请求到的菜单列表', res)
        this.list = res._items
        this.tree = this.convertMenuListToTree(this.list)
      })
    },
    onClickToEdit(data) {
      console.log('修改菜单', data)
    },
    onClickDeleteMenu(data) {},
    convertMenuListToTree(menuList) {
      const tree = []
      menuList.forEach((item, key) => {
        // console.log('---------', key, item)
        if (item.parent_id === 0) {
          item.children = []
          tree.push(item)
          // menuList.splice(key, 1)
          menuList.forEach((_item, _key) => {
            if (_item.parent_id === item.id) {
              item.children.push(_item)
              // menuList.splice(_key, 1)
            }
          })
        }
      })
      // console.log('menuList', menuList)
      // console.log('tree', tree)
      return tree
    }
  },
  mounted() {
    this.init()
  }
}
</script>
