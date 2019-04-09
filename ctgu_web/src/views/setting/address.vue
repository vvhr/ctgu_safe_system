<template>
  <div class="app-container-address">
    <div style="width: 500px">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px">
        <el-form-item label="当前默认地址">
          <el-button>{{default_address}}</el-button>
        </el-form-item>
        <el-form-item label="更改默认地址" prop="default_address">
          <addressComponent v-model="address"></addressComponent>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">确定修改</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
  import addressComponent from '../../components/address'
  import { setSetting } from '../../api/UserSetting'
  import { replyRes } from '../../utils/res'

  const defaultRuleForm = {
    default_address: ''
  }
  export default {
    components: { addressComponent },
    data() {
      return {
        ruleForm: Object.assign({}, defaultRuleForm),
        rules: {
          default_address: [
            { required: true, message: '请选择地址', trigger: 'blur' }
          ]
        },
        address: []
      }
    },
    computed: {
      default_address() {
        return this.$store.state.user.default_address
      }
    },
    methods: {
      submitForm(formName) {
        this.ruleForm.default_address = this.address.length > 0 ? JSON.stringify(this.address) : ''
        this.$refs[formName].validate((valid) => {
          if (valid) {
            setSetting(this.ruleForm).then(res => {
              if (replyRes(res)) {
                this.$store.commit('SET_Default_Address', res.bData.default_address)
                this.sendMsgToParent()
              }
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },
      sendMsgToParent() {
        this.$emit('listenToChildEvent', 'isChange')
      }
    }
  }
</script>
<style scoped>
  .app-container-address {
    padding: 20px;
    /* min-width: 1300px; */
    /*background: rgb(20, 40, 60);*/
    /*height: 100%;*/
    /*width: 600px;*/
  }
</style>
