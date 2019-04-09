<template>
  <div class="app-container">
    <div><el-button @click="onClickBulkAddApi">批量更新api列表</el-button></div>
    <!--用户组列表-->
    <table class="_table">
      <tr>
        <th>id</th>
        <th>名称</th>
        <th>操作</th>
      </tr>
      <tr v-for="(v,k) in listUserGroup" :key="k">
        <td>{{v.id}}</td>
        <td>{{v.group_name}}</td>
        <td>
          <el-button @click="onClickToEditPermission(v)">权限</el-button>
        </td>
      </tr>
    </table>
    <!--权限编辑对话框-->
    <el-dialog v-if="activeGroup!==null" :title="'【'+activeGroup.group_name + '】 用户组接口权限编辑'" :visible.sync="visible.userPermissionEditForm" width="90%">
      <user_group_api :user_group="activeGroup"></user_group_api>
    </el-dialog>
  </div>
</template>

<script>
  import { getUserGroups } from '../../api/userGroup'
  import { bulkAddApi } from '../../api/api'
  import user_group_api from './components/user_group_api'
  import { replyRes } from '../../utils/res'

  export default {
    components: { user_group_api },
    data() {
      return {
        listUserGroup: [],
        visible: {
          userPermissionEditForm: false
        },
        activeGroup: null
      }
    },
    methods: {
      init() {
        this.fetchListUserGroup()
      },
      fetchListUserGroup() {
        getUserGroups().then(res => {
          console.log(res)
          this.listUserGroup = res._items
        })
      },
      onClickToEditPermission(group) {
        this.activeGroup = group
        this.visible.userPermissionEditForm = true
      },
      onClickBulkAddApi() {
        bulkAddApi().then(res => {
          console.log(res)
          replyRes(res)
        })
      }
    },
    mounted() {
      this.init()
    }
  }
</script>
