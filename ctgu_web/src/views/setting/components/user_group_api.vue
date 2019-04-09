<template>
  <div>
    <!--接口列表-->
    <!--<div style="height: 50px; background: black;color: orange">{{selectedApiId}}</div>-->
    <el-form label-width="0">
      <el-form-item>
        <el-button @click="onClickUpdate" type="danger">更新权限</el-button>
      </el-form-item>
      <el-form-item>
        <el-checkbox-group v-model="selectedApiId">
          <el-checkbox-button v-for="item in allApi" :label="item.id" name="api" :key="item.id" style="margin: 5px;">{{'【' + item.comment + '】 ' + item.api}}</el-checkbox-button>
        </el-checkbox-group>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import { getUserGroupApi, updateUserGroupApi } from '../../../api/userGroupApi'
  import { getApiList } from '../../../api/api'
  import { replyRes } from '../../../utils/res'

  export default {
    name: 'user_group_api',
    data() {
      return {
        selectedApiId: [],
        listApi: [],
        allApi: []
      }
    },
    props: {
      user_group: {
        type: Object,
        required: true,
        default() {
          return {}
        }
      }
    },
    mounted() {
      console.log('user_group_api组件的mounted，组件mounted勾子')
      this.fetchAllApi()
      this.init()
    },
    watch: {
      user_group() {
        console.log('user_group_api组件的watch：user_group变量，组件watch勾子')
        this.init()
      }
    },
    methods: {
      init() {
        this.fetchApi()
      },
      fetchApi() {
        getUserGroupApi({ group_id: this.user_group.id, expand: 'apiInfo' }).then(res => {
          this.listApi = res._items
          this.selectedApiId = this.convertListApiToArr(this.listApi)
        })
      },
      fetchAllApi() {
        getApiList().then(res => {
          this.allApi = res._items
        })
      },
      convertListApiToArr(listApi) {
        const arr = []
        listApi.forEach(function(item) {
          arr.push(item.api_id)
        })
        return arr
      },
      onClickUpdate() {
        updateUserGroupApi({ group_id: this.user_group.id, api_ids: this.selectedApiId }).then(res => {
          replyRes(res)
          this.fetchApi()
        })
      }
    }
  }
</script>
