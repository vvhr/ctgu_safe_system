<template>
  <div class="app-container">
    <div>
      <!--声音文件-->
      <audio
        src="/static/voice.wav"
        id="voice">
        你的浏览器不支持audio标签
      </audio>
      <audio
        src="/static/关闭.m4a"
        id="voice-close">
      </audio>
      <audio
        src="/static/打开.m4a"
        id="voice-open">
      </audio>
      <audio
        src="/static/电吹风.m4a"
        id="voice-dcf">
      </audio>
      <audio
        src="/static/CFL灯.m4a"
        id="voice-cfl">
      </audio>
      <audio
        src="/static/LED灯.m4a"
        id="voice-led">
      </audio>
      <audio
        src="/static/电磁炉.m4a"
        id="voice-dcl">
      </audio>
      <audio
        src="/static/电动车.m4a"
        id="voice-ddc">
      </audio>
      <audio
        src="/static/热得快.mp3"
        id="voice-rdk">
      </audio>
      <audio
        src="/static/热水壶.m4a"
        id="voice-rsh">
      </audio>
      <audio
        src="/static/热水器.m4a"
        id="voice-rsq">
      </audio>
      <audio
        src="/static/微波炉.m4a"
        id="voice-wbl">
      </audio>
      <audio
        src="/static/浴霸.m4a"
        id="voice-yb">
      </audio>
    </div>
    <!--第二行：电器开关展示 新-->
    <div class="app-container-row-02-new">
      <div class="app-container-row-center">
        <el-row>
          <div class="app-container-row-center-top scroll-bar">
            <ul>
              <li style="color: #999;width: 350px">
                <div style="text-align: center">
                  <a class="label-text" style="background-color: rgb(64, 92, 133); padding: 5px">实时参数</a>
                </div>
                <el-row style="margin-top: 15px">
                   <el-button type="primary" class="label-text-son">当前时间</el-button>
                   <el-button type="text" style="padding: 2px;color: #0dfdf3">{{refreshTime.getFullYear() + '-' + (refreshTime.getMonth()+1) + '-' + refreshTime.getDate() + ' ' + refreshTime.getHours()+ ':' + refreshTime.getMinutes()+ ':' + refreshTime.getSeconds()}}</el-button>
                </el-row>
                <el-row style="margin-top: 15px">
                  <el-button type="primary" class="label-text-son" >功率</el-button>
                  <el-button type="text" style="padding: 2px;color: #0dfdf3" >{{deviceReport.p}} W</el-button>
                </el-row>
                <el-row style="margin-top: 15px">
                    <el-button type="primary" class="label-text-son" >电流</el-button>
                    <el-button type="text" style="padding: 2px;color: #0dfdf3" >{{deviceReport.c/100}} A</el-button>
                </el-row>
                <el-row style="margin-top: 15px">
                    <el-button type="primary" class="label-text-son" >电压</el-button>
                   <el-button type="text" style="padding: 2px;color: #0dfdf3" >{{deviceReport.v/100}} V</el-button>
                </el-row>
                <el-row style="margin-top: 15px">
                  <el-button type="primary" class="label-text-son" >漏电流</el-button>
                  <el-button type="text" style="padding: 2px;color: #0dfdf3" >{{deviceReport.lc}} mA</el-button>
                </el-row>
                <el-row style="margin-top: 15px">
                  <el-button type="primary" class="label-text-son" >温度</el-button>
                  <el-button type="text" style="padding: 2px;color: #0dfdf3" >{{deviceReport.t/10}} C</el-button>
                </el-row>
              </li>
            </ul>
          </div>
        </el-row>
      </div>
      <div class="app-container-row-right scroll-bar">
        <ul style="padding-left: 0px">
          <li style="background-color: rgba(64, 92, 133, 0.65);height: 38px;position: relative;top: -10px;">
            <a class="label-text" style="margin-left: 25px;margin-right: 15px; padding: 4px">监测功率跃迁值</a>
            <el-radio v-model="radio" label="1" style="color: #d9ecff">(1W)</el-radio>
            <el-radio v-model="radio" label="5" style="color: #d9ecff">(5W)</el-radio>
            <el-radio v-model="radio" label="20" style="color: #d9ecff">(20W)</el-radio>
            <el-radio v-model="radio" label="50" style="color: #d9ecff">(50W)</el-radio>
            <el-popover
              placement="top-start"
              title="漏电流（LC）超限提醒"
              width="300"
              trigger="hover">
              <ul style="background-color: rgba(38, 74, 101, 0.62);padding: 5px">
                <li style="color: #d9ecff;margin-top: 5px"><el-tag size="mini" type="primary" class="tag-type-safe">摆脱电流</el-tag>
                  - 10mA <= LC < 30mA </li>
                <li style="color: #d9ecff;margin-top: 5px"><el-tag size="mini" type="primary" class="tag-type-general">触电风险</el-tag>
                  - 30mA <= LC < 50mA </li>
                <li style="color: #d9ecff;margin-top: 5px"><el-tag size="mini" type="primary" class="tag-type-bad">致命风险</el-tag>
                  - 50mA <= LC < 300mA</li>
                <li style="color: #d9ecff;margin-top: 5px"><el-tag size="mini" type="primary" class="tag-type-serious">火灾警告</el-tag>
                  - LC >= 300mA</li>
              </ul>
              <el-button type="text" style="margin-left: 100px;color: #0dfdf3" icon="el-icon-info" slot="reference">漏电提醒预览</el-button>
            </el-popover>
          </li>
          <li style="color: #999" v-for="(p , key) in power">
            <el-row>
              <el-col :span="8" style="text-align: left;margin-left: 15px">
                <el-button type="danger" size="mini" style="padding: 2px" v-if="key === power.length - 1">New</el-button>
                <!--<el-tag size="mini" type="danger" v-if="key === power.length - 1">New</el-tag>-->
                <a style="color: #d9ecff">* 事件发生时间</a>
                <el-tag size="mini" type="success" style="margin-left: 5px">{{p.date}}</el-tag>
              </el-col>
              <el-col :span="3" style="text-align: center">
                <a style="color: #d9ecff">功率</a>
                <el-tag size="mini" type="success" style="margin-left: 5px">{{p.power}}W</el-tag>
              </el-col>
              <el-col :span="3" style="text-align: center">
                <a style="color: #d9ecff">差值</a>
                <el-tag size="mini" type="warning" style="margin-left: 5px" v-if="p.diffP>0">{{p.diffP}}W</el-tag>
                <el-tag size="mini" type="primary" style="margin-left: 5px" v-else-if="p.diffP<0">{{-p.diffP}}W</el-tag>
                <el-tag size="mini" type="primary" style="margin-left: 5px" v-else>{{-p.diffP}}W</el-tag>
              </el-col>
              <el-col :span="3" style="text-align: center">
                <el-tag size="mini" type="warning" style="width: 80px; " v-if="p.powerChangeType===1">功率抬升</el-tag>
                <el-tag size="mini" type="primary" style="width: 80px; " v-else-if="p.powerChangeType===2">功率下降</el-tag>
              </el-col>
              <el-col :span="3" style="text-align: center">
                <a style="color: #d9ecff">漏电流</a>
                <el-tag size="mini" type="success" style="margin-left: 5px" v-if="p.lc<10">{{p.lc}}mA</el-tag>
                <el-tag size="mini" type="primary" style="color: seashell; margin-left: 5px" v-else-if="p.lc>=10 && p.lc<30">{{p.lc}}mA</el-tag>
                <el-tag size="mini" type="warning" style="color: #f5dab1; margin-left: 5px" v-else-if="p.lc>=30 && p.lc<50">{{p.lc}}mA</el-tag>
                <el-tag size="mini" type="warning" style="margin-left: 5px" v-else-if="p.lc>=50 && p.lc<300">{{p.lc}}mA</el-tag>
                <el-tag size="mini" type="danger" style="margin-left: 5px" v-else-if="p.lc>=300">{{p.lc}}mA</el-tag>
              </el-col>
              <el-col :span="3" style="text-align: center">
                <el-tag size="mini" type="primary" class="tag-type-safe" v-if="p.lc>=10 && p.lc<30">摆脱电流</el-tag>
                <el-tag size="mini" type="primary" class="tag-type-general" v-else-if="p.lc>=30 && p.lc<50">触电风险</el-tag>
                <el-tag size="mini" type="primary" class="tag-type-bad" v-else-if="p.lc>=50 && p.lc<300">致命风险</el-tag>
                <el-tag size="mini" type="primary" class="tag-type-serious" v-else-if="p.lc>=300">火灾警告</el-tag>
              </el-col>
            </el-row>
          </li>
        </ul>
      </div>
    </div>
    <!--第三行-->
    <div class="app-container-row-03-new" style="margin-top: 10px">
      <el-row>
        <div style="position: relative;width: 100%;height: 502px;margin: 0 auto;">
          <chart :uuid="uuid" :reportData="reportData" :flag_watched_by_chart="flag_watched_by_chart" height="500px" width="1800px"/>
        </div>
      </el-row>
    </div>
  </div>
</template>

<script>
  import { getDeviceReport } from '../../../api/deviceReportNew'
  import Chart from '../../../components/Charts/mixChart'
  import { parseTime } from '../../../utils'

  export default {
    name: 'homeApp',
    components: { Chart },
    props: {
      uuid: 0
    },
    data() {
      return {
        myOpenAppIds: [],
        myOpenAppIdsOld: [],
        myApps: {},
        myAppEventsHistory: [],
        myOpenAppOld: [],
        refreshTime: new Date(),
        ifCanSetTimeOut: true,
        enableVoicePlay: true,
        deviceReport: [],
        canDo: true,
        homeInfo: null,
        timer: null,
        eventTypes: {
          1: '开启',
          2: '关闭'
        },
        power_first: null,
        time_first: null,
        powerChangeType: 0,
        power: [],
        activeUuid: null,
        reportData: {
          power_arr: [],
          time_arr: [],
          current_arr: [],
          voltage_arr: []
        },
        flag_watched_by_chart: -1,
        radio: '1'
      }
    },
    methods: {
      init() {
        this.myOpenAppIds = []
        this.myOpenAppIdsOld = []
        this.myAppEventsHistory = []
        this.power = []
        this.reportData = {
          power_arr: [],
          time_arr: [],
          current_arr: [],
          voltage_arr: []
        }
        this.fetchDeviceReport()
        this.fetchHomeAppliances()
      },
      // 获取本通道的实时上报信息
      fetchDeviceReport() {
        getDeviceReport({ uuid: this.uuid }).then(res => {
          this.deviceReport = res
          // 计算功率变化
          this.calculatedPowerChange()
          if (this.ifCanSetTimeOut) {
            setTimeout(this.fetchDeviceReport, 1500)
          }
          console.log('获取到最新实时上报', res)
        })
      },
      calculatedPowerChange() {
        let diffP = 0
        let diffTime = 0
        if (this.power_first !== null) {
          diffP = this.deviceReport.p - this.power_first // 计算功率差值
        }
        this.power_first = this.deviceReport.p // 缓存功率到状态

        if (this.time_first !== null && this.deviceReport.reportTime > this.time_first) {
          diffTime = 1
        }
        this.time_first = this.deviceReport.reportTime
        if (diffP > Number(this.radio) && diffTime > 0) { // 如果功率差值超过预设, 且时间更新
          this.powerChangeType = 1
          this.flag_watched_by_chart *= -1
        } else if (-diffP > Number(this.radio)) {
          this.powerChangeType = 2
          this.flag_watched_by_chart *= -1
        } else this.powerChangeType = 0
        if (this.powerChangeType !== 0) {
          // 更新变化历史数组this.power
          if (this.power.length > 11) this.power.shift()
          // const date = this.refreshTime.getFullYear() + '-' + (this.refreshTime.getMonth() + 1) + '-' + this.refreshTime.getDate() + ' ' + this.refreshTime.getHours() + ':' + this.refreshTime.getMinutes() + ':' + this.refreshTime.getSeconds()
          const date = this.deviceReport.reportTime
          // const time = this.refreshTime.getHours() + ':' + this.refreshTime.getMinutes() + ':' + this.refreshTime.getSeconds()
          const time = parseTime(date, '{h}:{i}:{s}')
          const power = this.deviceReport.p
          const powerChangeType = this.powerChangeType
          const lc = this.deviceReport.lc
          const object = { date: date, power: power, powerChangeType: powerChangeType, time: time, diffP: diffP, lc: lc }
          this.power.push(object)
          // 更新图例历史数据组
          if (this.reportData.power_arr.length > 25) {
            this.reportData.power_arr.shift()
            this.reportData.time_arr.shift()
            this.reportData.current_arr.shift()
            this.reportData.voltage_arr.shift()
          }
          this.reportData.power_arr.push(power)
          this.reportData.time_arr.push(time)
          this.reportData.current_arr.push(this.deviceReport.c / 10)
          this.reportData.voltage_arr.push(this.deviceReport.v / 100)
        }
      }
    },
    mounted() {
      this.init()
    },
    watch: {
      uuid(value) {
        clearTimeout(this.timer)
        if (value === '0') {
          this.ifCanSetTimeOut = false
        } else {
          this.ifCanSetTimeOut = true
          this.init()
        }
      }
    },
    beforeDestroy() {
      this.ifCanSetTimeOut = false
      clearTimeout(this.timer)
    }
  }
</script>
<style scoped>
  /*.app-container{background: #062d47;min-height: 895px;color: white}*/
  .app-container{background: #062d4700;color: white}
  /*div{border: dashed 1px #aaa}*/
  ul{list-style: none;padding-left: 10px;}

  .shadow{background: #313438;color: #eee;border-radius: 10px;box-shadow:1px 2px 5px black;margin-bottom: 10px}
  .label-text{color: #0dfdf3;text-align: center}
  .label-text-son{width: 60px; padding: 2px; margin: 2px;font-size: 13px}

  .app-container-row-02 ul{padding:0}

  .app-container-row-02-new{
    display: -webkit-flex;
    display: flex;
    -webkit-justify-content: space-around;
    justify-content: space-around;
  }
  .app-container-row-left{width: 450px;height: 337px;margin-bottom: 30px;background-color: rgba(38, 74, 101, 0.62);overflow-x: hidden;overflow-y: scroll}
  .app-container-row-right{width: 1000px;height: 337px;padding-left: 00px;background-color: rgba(38, 74, 101, 0.62);overflow-x: hidden;overflow-y: scroll}

  .app-container-row-center{height: 340px;background-color: rgba(38, 74, 101, 0.62)}
  .app-container-row-center-top{width: 380px;height: 148px;}
  .app-container-row-center-bottom{width: 380px;height: 170px;margin-top: 10px;padding: 5px;background-color: rgba(0, 104, 144, 0.45);overflow-x: hidden;overflow-y: scroll}

  .app-container-row-02 .appliance{width: 350px;height:138px;display: flex;line-height: 23px;padding: 10px;margin:10px 15px 10px 0;border: solid 1px #0dfdf3}
  .app-container-row-02 .run-status{height: 92px;width: 92px;border-radius: 92px;background: #555;color:#0dfdf3;border:solid 1px #0dfdf3;line-height: 92px;text-align: center;margin: 16px 10px;font-size: 22px;font-weight: bolder}
  .app-container-row-02 .run-status-high{height: 92px;width: 92px;border-radius: 92px;background: #b13737;color:#0dfdf3;border:solid 1px #0dfdf3;line-height: 92px;text-align: center;margin: 16px 10px;font-size: 22px;font-weight: bolder}
  .app-container-row-02 .run-status-risk{height: 92px;width: 92px;border-radius: 92px;background: red;color:#0dfdf3;border:solid 1px #0dfdf3;line-height: 92px;text-align: center;margin: 16px 10px;font-size: 22px;font-weight: bolder}

  .app-container-row-03 ul li{line-height: 20px;}

  .scroll-bar::-webkit-scrollbar {
    width: 10px;     /*高宽分别对应横竖滚动条的尺寸*/
    height: 1px;
  }
  .scroll-bar::-webkit-scrollbar-thumb {/*滚动条里面小方块*/
    border-radius: 15px;
    -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
    background: #97a8be;
  }
  .scroll-bar::-webkit-scrollbar-track {/*滚动条里面轨道*/
    -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
    border-radius: 15px;
    background: #EDEDED;
  }

  .tag-type-safe {
    background-color: #AE8F00; width: 80px; text-align: center; color: #d9ecff;
  }
  .tag-type-general {
    background-color: #D26900; width: 80px; text-align: center; color: #d9ecff;
  }
  .tag-type-bad {
    background-color: #BB3D00; width: 80px; text-align: center; color: #d9ecff;
  }
  .tag-type-serious {
    background-color: #AE0000; width: 80px; text-align: center; color: #d9ecff;
  }

</style>
<style>
  .el-dialog.is-fullscreen {
    overflow: hidden;
  }
</style>
