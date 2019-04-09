import { Message } from 'element-ui'
import { _join2 } from './string'

// 请求组件返回消息处理，并弹出消息框公用处理方法
export function replyRes(data) {
  console.log('replyRes res>>', data)
  // console.log(res)
  if (data.bCode === 101) {
    if (data.bMsg !== undefined) {
      Message({ message: data.bMsg, type: 'success' })
    } else {
      Message({ message: '操作成功', type: 'success' })
    }
    return true
  } else if (data.bCode === 104) {
    let errorMsg = '操作失败'
    if (data.bMsg !== undefined) {
      errorMsg = data.bMsg
    }
    console.log(typeof (data.bData))
    if (typeof (data.bData) === 'object' && data.bData !== null) {
      errorMsg = _join2(data.bData)
    } else if (typeof (data.bData) === 'string') {
      errorMsg = data.bData
    }
    Message({ message: errorMsg, type: 'error' })
  } else {
    Message({ message: '返回状态码非101 104', type: 'error' })
  }
  return false
}

/**
 * @param originalList // 表示从接口获取的一组上报记录，每一条记录包括了四个通道的电流电压温度等信息
 * @returns {Array} // 返回的数组就是原始数据中一条记录拆分成四条记录。所以行数应该是原始数据行数的四倍
 */
export function toChannel(availableChannels, originalList) {
  // 需要哪些通道的数据
  /**
   * ！！！因为调试阶段，只开通道1，其它先屏蔽掉,后续生产环节要恢复开启
   */
  const res = []
  for (const index in originalList) {
    if (availableChannels.indexOf(1) >= 0) {
      const channel1 = {
        imei: originalList[index].imei,
        channel: 1,
        voltage: originalList[index].voltage1,
        electricity: originalList[index].electricity,
        leakageCurrent: originalList[index].leakageCurrent,
        temperature: originalList[index].temperatureA,
        // ----------整个数组放到一个额外元素info中
        info: originalList[index]
      }
      res.push(channel1)
    }
    if (availableChannels.indexOf(2) >= 0) {
      const channel2 = {
        imei: originalList[index].imei,
        channel: 2,
        voltage: originalList[index].voltage2,
        electricity: originalList[index].electricity2,
        leakageCurrent: originalList[index].leakageCurrent2,
        temperature: originalList[index].temperatureB,
        info: originalList[index]
      }
      res.push(channel2)
    }
    if (availableChannels.indexOf(3) >= 0) {
      const channel3 = {
        imei: originalList[index].imei,
        channel: 3,
        voltage: originalList[index].voltage3,
        electricity: originalList[index].electricity3,
        leakageCurrent: originalList[index].leakageCurrent3,
        temperature: originalList[index].temperatureC,
        info: originalList[index]
      }
      res.push(channel3)
    }
    if (availableChannels.indexOf(4) >= 0) {
      const channel4 = {
        imei: originalList[index].imei,
        channel: 4,
        voltage: originalList[index].voltage4,
        electricity: originalList[index].electricity4,
        leakageCurrent: originalList[index].leakageCurrent4,
        temperature: originalList[index].temperatureN,
        info: originalList[index]
      }
      res.push(channel4)
    }
  }
  return res
}
