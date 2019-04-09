<template>
  <div>
    <!--分页区-->
    <div class="block">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pageInfo.pageNum"
        :page-sizes="[5]"
        :page-size="5"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pageInfo.total">
      </el-pagination>
    </div>
    <table class="_table">
      <thead>
      <tr>
        <th>imei</th>
        <th>lon</th>
        <th>lat</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in deviceList" :key="key">
       <td>{{item.imei}}</td>
       <td>{{item.lon}}</td>
       <td>{{item.lat}}</td>
        <td>
          <el-button @click="onClickSetPositionBtn(item)">修改坐标</el-button>
        </td>
      </tr>
      </tbody>
    </table>
    <!--修改坐标对话框-->
    <el-dialog
      @open="handOpenSetPositionDialog"
      :append-to-body="true"
      :title="'修改设备坐标：' + activeItem.imei"
      :visible.sync="visible.setPosition">
      <span>lon: {{position.lon}} / lat: {{position.lat}}</span>
      <div id="map-container"></div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="visible.setPosition = false">取 消</el-button>
        <el-button type="primary" @click="submitPosition">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import { getDeviceByProjectId, setDeviceLocation } from '../../../api/device'
  import { addMarker, createMap } from '../../../utils/AMap'

  export default {
    name: 'deviceList',
    data() {
      return {
        map: null,
        marker: null,
        visible: {
          setPosition: false
        },
        position: {
          lon: 0,
          lat: 0
        },
        deviceList: [],
        activeItem: {},
        // 页码信息
        pageInfo: {
          size: 5,
          pageNum: 1,
          total: 0
        },
        mapOption: {
          zoom: 18,
          // zooms: [4, 18],
          center: [114.070719, 22.658496],
          layers: [],
          viewMode: '2D'
        }
      }
    },
    props: {
      projectId: 0
    },
    mounted() {
      this.fetchDeviceByProjectId()
    },
    watch: {
      projectId(val) {
        //  console.log('projectId', val)
        this.fetchDeviceByProjectId(val)
      }
    },
    methods: {
      fetchDeviceByProjectId() {
        getDeviceByProjectId({ projectId: this.projectId, pageNum: this.pageInfo.pageNum, size: this.pageInfo.size }).then(res => {
          this.deviceList = res.data.list
          this.pageInfo.total = res.data.totalPageNum * this.pageInfo.size
          //  console.log('弹框中的子组件：获取项目关联的设备列表：fetchDeviceByProjectId', res)
        })
      },
      handleSizeChange(size) {
        //  console.log('change page size', size)
        this.pageInfo.size = size
        this.fetchDeviceByProjectId()
      },
      handleCurrentChange(pageNum) {
        //  console.log('change pageNum', pageNum)
        this.pageInfo.pageNum = pageNum
        this.fetchDeviceByProjectId()
      },
      handOpenSetPositionDialog() {
        // 只有第一次打开对话框时或者地图api脚本未加载时执行一次下载脚本并创建地图实例，同时也只创建一个标记物对象。后面只是移位
        if (this.map === null) {
          createMap('map-container', this.mapOption).then(mapInstance => {
            console.log('第一次调用工具成功得到地图实例')
            // 获取地图实例
            this.map = mapInstance
            // 制作第一个标记物
            this.marker = addMarker(this.map, this.activeItem.imei, '_marker_0', [this.activeItem.lon, this.activeItem.lat], (imei) => {
              alert(imei)
            })
            console.log('第一次添加标记物')
            // 当点击地图时，获取地图点击坐标,并移动标记物位置
            this.map.on('click', (ev) => {
              //  console.log('点击地图获取坐标', ev.lnglat)
              this.position.lon = ev.lnglat.lng
              this.position.lat = ev.lnglat.lat
              this.marker.setPosition([this.position.lon, this.position.lat])
            })
          })
        } else {
          // 当地图实例，与唯一标记物已存在时，只需要改变标记物位置
          this.marker.setPosition([this.activeItem.lon, this.activeItem.lat])
        }
      },
      onClickSetPositionBtn(item) {
        this.activeItem = item
        this.position.lon = this.activeItem.lon
        this.position.lat = this.activeItem.lat
        this.visible.setPosition = true
      },
      submitPosition() {
        //  console.log('submitPosition', this.position, this.activeImei)
        setDeviceLocation(Object.assign(this.position, { imei: this.activeItem.imei })).then(res => {
          //  console.log('更改设备坐标完成', res)
          this.$message({
            message: '设备[' + this.activeItem.imei + ']坐标修改成功',
            type: 'success'
          })
          this.marker.setPosition([this.position.lon, this.position.lat])
          this.fetchDeviceByProjectId(this.finalSearchForm)
          this.visible.setPosition = false
        })
      }
    }
  }
</script>
<style>
  #map-container {border: solid 1px #a0a0a0;height: 480px; width: 910px;}
  ._marker_0{color:white;background: green;width: 20px;height:20px;text-align: center;border-radius: 20px;border: solid 1px #fff;line-height: 20px}
  ._marker_1{color:black;background: yellow;width: 20px;height:20px;text-align: center;border-radius: 20px;border: solid 1px #fff;line-height: 20px}
  ._marker_2{color:white;background: red;width: 20px;height:20px;text-align: center;border-radius: 20px;border: solid 1px #fff;line-height: 20px}
</style>
