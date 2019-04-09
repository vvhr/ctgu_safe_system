<template>
  <div class="login-container">
    <div class="login-title">
      <!--标题-->
    </div>
    <el-form class="login-form" autoComplete="on" :model="loginForm" :rules="loginRules" ref="loginForm" label-position="left">
      <div class="login-input">
        <!--选择框-->
        <el-form-item prop="role">
          <span class="svg-container svg-container_login"><svg-icon icon-class="example" /></span>
          <el-select v-model="loginForm.role_id" placeholder="请选择" size="small" style="width: 250px">
            <div class="login-role-select">
              <el-option
                v-for="item in roles"
                :key="item.id"
                :label="item.name"
                :value="item.id">
              </el-option>
            </div>
          </el-select>
        </el-form-item>
        <!--登录框-->
        <el-form-item prop="username">
          <span class="svg-container svg-container_login"><svg-icon icon-class="user" /></span>
          <el-input size="small" name="username" type="text" v-model="loginForm.username" autoComplete="off" placeholder="请输入用户名" />
        </el-form-item>
        <!--密码框-->
        <el-form-item prop="password" style="margin-bottom: 20px">
          <span class="svg-container svg-container_login"><svg-icon icon-class="password"></svg-icon></span>
          <el-input size="small" name="password" :type="pwdType" @keyup.enter.native="handleLogin" v-model="loginForm.password" autoComplete="on"
                    placeholder="请输入密码"></el-input>
          <span class="show-pwd" @click="showPwd"><svg-icon icon-class="eye" /></span>
        </el-form-item>
      </div>
      <!--按钮-->
      <el-button size="small" style="width: 300px;" type="primary" :loading="loading" @click.native.prevent="handleLogin">
        登 录
      </el-button>
    </el-form>
  </div>
</template>

<script>
  import { isvalidUsername } from '@/utils/validate'

  export default {
    name: 'login',
    data() {
      const validateUsername = (rule, value, callback) => {
        if (!isvalidUsername(value)) {
          callback(new Error('请输入正确的用户名'))
        } else {
          callback()
        }
      }
      const validatePass = (rule, value, callback) => {
        if (value.length < 5) {
          callback(new Error('密码错误'))
        } else {
          callback()
        }
      }
      return {
        roles: [
          { id: 0, name: '请选择登录角色' },
          { id: 1, name: '居民 Personal User' },
          { id: 2, name: '企业 Business User' },
          { id: 3, name: '政府 Government user' },
          { id: 4, name: '内部 Admin user' }
        ],
        loginForm: {
          username: '',
          password: '',
          role_id: 3
        },
        loginRules: {
          username: [{ required: true, trigger: 'blur', validator: validateUsername }],
          password: [{ required: true, trigger: 'blur', validator: validatePass }]
        },
        loading: false,
        pwdType: 'password'
      }
    },
    methods: {
      // 切换密码显示
      showPwd(a, b, c) {
        if (this.pwdType === 'password') {
          this.pwdType = ''
        } else {
          this.pwdType = 'password'
        }
      },
      // 视图
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid) {
            this.loading = true
            this.$store.dispatch('Login', this.loginForm).then(() => {
              this.loading = false
              this.$router.push({ path: '/' })
            }).catch(() => {
              this.loading = false
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  $bg:#2d3a4b;
  $light_gray:#eee;

  /* reset element-ui css */
  .login-container {
    .el-input {
      display: inline-block;
      height: 47px;
      width: 85%;
      input {
        /*background: transparent;*/
        border: 0;
        -webkit-appearance: none;
        border-radius: 0;
        padding: 12px 5px 12px 15px;
        color: $light_gray;
        height: 47px;
        &:-webkit-autofill {
          -webkit-box-shadow: 0 0 0px 1000px $bg inset !important;
          -webkit-text-fill-color: #fff !important;
        }
      }
    }
    .el-form-item {
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(0, 0, 0, 0.1);
      border-radius: 5px;
      margin-bottom: 5px;
      color: #454545;
      width: 300px;
    }

  }

  .login-container .login-input {

    .el-input input {
      /* background: transparent; */
      margin-left: 10px;
      width: 180px;
      border: 0;
      -webkit-appearance: none;
      border-radius: 0;
      padding: 5px 5px 5px 5px!important;
      color: #12fafb;
      height: 35px!important;
      background-color: #2d3a4b;
    }

    .el-form-item__content {
      line-height: 0;
      position: relative;
      font-size: 14px;
      height: 35px;
      background: #2d3a4b;
    }
  }


  .el-popper[x-placement^=bottom] {
    border: 0;
    padding: 0;
    margin: 0;
  }

  .login-role-select {

    .el-select-dropdown__item {
      font-size: 14px;
      padding: 0 20px;
      position: relative;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #ffffff;
      height: 34px;
      line-height: 34px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      cursor: pointer;
      background-color: #114a6f;
    }


    .el-select-dropdown__item.selected {
      color: #44dae2;
      font-weight: 100;
    }

    .el-select-dropdown__item.hover, .el-select-dropdown__item:hover {
      background-color: #2d3a4b;
    }

  }
</style>
<style rel="stylesheet/scss" lang="scss" scoped>
  $bg:#2d3a4b;
  $dark_gray:#889aa4;
  $light_gray:#eee;
  $technology_blue: #12fafb;
  .login-container {
    position: fixed;
    height: 100%;
    width: 100%;
    /** 色素条横轴平铺背景 */
    /*background: url('../../../static/image/bg_num1.jpg') left top repeat-x #082648;*/
    /** 大图背景 **/
    background: url('../../../static/image/login.jpg') no-repeat;
    background-size: cover;
    .login-form {
      border: 2px solid rgba(68, 226, 234, 0.42);
      background-color: rgba(1, 39, 62, 0);
      border-radius: 5px;
      position: absolute;
      left: 800px;
      right: 0;
      width: 350px;
      margin: 10px auto;
      top: 400px;
      padding: 15px 25px;
    }
    .tips {
      font-size: 14px;
      color: #fff;
      margin-bottom: 10px;
      span {
        &:first-of-type {
          margin-right: 16px;
        }
      }
    }
    .svg-container {
      padding: 6px 5px 6px 15px;
      color: $dark_gray;
      vertical-align: middle;
      width: 30px;
      display: inline-block;
      &_login {
        font-size: 20px;
      }
    }
    .login-title {
      background: url('../../../static/image/login_title.png');
      position: fixed;
      left: 1170px;
      right: 0;
      width: 400px;
      height: 100px;
      top: 300px;
      font-size: 16px;
      color: $technology_blue;
      text-align: center;
      /*font-weight: bold;*/
    }
    .show-pwd {
      position: absolute;
      right: 10px;
      top: 7px;
      font-size: 16px;
      color: $dark_gray;
      cursor: pointer;
      user-select: none;
    }
  }
</style>
