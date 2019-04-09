/**
 * 高德地图异步加载组件
 * @returns {Promise<any>}
 */
export function loadAMap() {
  // 定义常量
  const KEY = '89f4ba7c81426481728efc4543345a5c'
  // 创建promise对象并返回
  return new Promise((resolve, reject) => {
    // 当AMap对象存在时，解析AMap对象
    // eslint-disable-next-line
    if (typeof AMap !== 'undefined') {
      // console.log('在AMap工具中检测到地图类工具实例AMap已经存在')
      // eslint-disable-next-line
      resolve(AMap)
    }

    // 如果全局方法从未定义过：由定义全局地图资原加载回调方法:加载完结后，解析AMap对象
    if (typeof onAMapLoad === 'undefined') {
      window.onAMapLoad = () => {
        console.log('onMapLoad:高德地图JSSDK加载完成')
        // eslint-disable-next-line
        resolve(AMap)
      }
      const AMapUrl = 'https://webapi.amap.com/maps?v=1.4.7&key=' + KEY + '&plugin=AMap.DistrictSearch&callback=onAMapLoad'
      // 在HTML文档中插入JSSDK脚本链接.当资源初次异步加载（Promise会等待加载完成）完成后，会调用onAMapLoad进行解析,
      // ***解析完成(resolve)表示Promise使命完成***
      const scriptNode = document.createElement('script')
      scriptNode.setAttribute('type', 'text/javascript')
      scriptNode.setAttribute('src', AMapUrl)
      document.body.appendChild(scriptNode)
      // 如果已定义过该全局方法，每隔0.1秒检测是否AMap已加载，没有再重复检测，如已经加载则解析AMap,异步操作结束
    } else {
      // 检测是否存在AMap
      const checkHasAMap = () => {
        if (typeof AMap === 'undefined') {
          console.log('loadMap:第二次调用,循环检测AMap在是否存在')
          setTimeout(checkHasAMap, 100)
        } else {
          // eslint-disable-next-line
          resolve(AMap)
        }
      }
      checkHasAMap()
    }
  })
}
// 返回Promise，解析状态为地图实例
export function createMap(el, mapOption) {
  // 创建地图实例
  return loadAMap().then(_AMap => {
    console.log('createMap:创建地图画布实例并返回')
    return new _AMap.Map('map-container', mapOption)
  })
}

// position:[116.39, 39.9] index: vue子组件状态：markers集合的索引 markerClass:各色的样式，定义在sytle中
export function addMarker(mapInstance, imei, colorStyleClass, position, onclickCallBack) {
  // eslint-disable-next-line
  const marker = new AMap.Marker({
    position: position,
    content: '<div class="' + colorStyleClass + '">' + 'R' + '</div>'
  })
  mapInstance.add(marker)
  // 添加标记物事件
  marker.on('click', (event) => {
    onclickCallBack(imei)
  })
  console.log('addMarker:添加标记物完成')
  return marker
}

export function removeMarker(mapInstance, marker) {
  mapInstance.remove(marker)
  delete this.markers[marker]
  console.log('removeMarker：delete marker success')
}
// selectedAddress: 一个由各级地址组成的一元数组
// return: 一个由省市区为键名，以具体行政区域名组成的对象
export function selectedAddressToParams(selectedAddress) {
  const params = {}
  for (const i in selectedAddress) {
    switch (i) {
      case '0':
        params.province = selectedAddress[0]
        break
      case '1':
        params.city = selectedAddress[1]
        break
      case '2':
        params.district = selectedAddress[2]
        break
      case '3':
        params.township = selectedAddress[3]
        break
      default:
        break
    }
  }
  return params
}
