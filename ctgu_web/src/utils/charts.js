import echarts from 'echarts'
export function getChartInstance(elId) {
  return echarts.init(document.getElementById(elId))
}
// 只含一个数据系统，线状或柱形图
export function getChartOption(type, data, dataLabel, xAxisUnit) {
  console.log(data)
  const xAxisData = []
  const seriesData = []
  for (const index in data) {
    xAxisData.push(index + xAxisUnit)
    seriesData.push(data[index])
  }
  const option = {
    // color: 'rgb(0, 179, 249)',
    title: {
      text: ''
    },
    // 鼠标在图表内悬浮时，会提示当前指针所在位置的刻度值
    tooltip: {
      trigger: 'axis', // item
      axisPointer: {
        type: 'cross',
        label: {
          backgroundColor: '#6a7985'
        }
      }
    },
    // 图例，必须与数据系列的名称一致
    legend: {
      data: [dataLabel]
    },
    toolbox: {
      feature: {
        saveAsImage: {}
      }
    },
    // 定义图表区与边缘的空白距离：左边距，右边距，下边距
    grid: {
      left: '0',
      right: '1%',
      bottom: '1%',
      top: '35',
      width: '750',
      height: '150',
      containLabel: true
    },
    // 横坐标刻度名，需要与数据系列的键名一致（相同）
    xAxis: [
      {
        type: 'category',
        boundaryGap: false,
        data: xAxisData
      }
    ],
    yAxis: [
      {
        type: 'value'
      }
    ],
    // 数据系列，一组一维数组值。
    series: [
      {
        name: dataLabel,
        type: type,
        label: {
          normal: {
            show: false,
            position: 'top'
          }
        },
        areaStyle: {},
        data: seriesData
      }
    ]
  }
  return option
}

export function getChartOptionOfPie(chartTitle, seriesName, seriesData, pieColor, minMax, unit, backgroundColor = '#2c343c', radius = '60%') {
  const option = {
    backgroundColor: backgroundColor,

    title: {
      text: chartTitle,
      left: 'center',
      top: 20,
      textStyle: {
        color: '#ccc'
      }
    },
    tooltip: {
      trigger: 'item',
      formatter: '{a} <br/>{b} : {c} ({d}%)'
    },

    visualMap: {
      show: false,
      min: minMax[0],
      max: minMax[1],
      inRange: {
        colorLightness: [0, 1]
      }
    },
    series: [
      {
        name: seriesName,
        type: 'pie',
        radius: radius,
        center: ['50%', '50%'],
        data: seriesData.sort(function(a, b) { return a.value - b.value }),
        roseType: 'radius',
        label: {
          formatter: '{b}: {c} ' + unit + ' \r\n 占比{d}%',
          fontSize: 15,
          color: 'rgba(255, 255, 255, 0.7)'
        },
        labelLine: {
          normal: {
            lineStyle: {
              color: 'rgba(255, 255, 255, 0.5)'
            },
            smooth: 0.2,
            length: 10,
            length2: 20
          }
        },
        itemStyle: {
          normal: {
            color: pieColor,
            shadowBlur: 200,
            shadowColor: 'rgba(0, 0, 0, 1)'
          }
        },

        animationType: 'scale',
        animationEasing: 'elasticOut',
        animationDelay: function(idx) {
          return Math.random() * 200
        }
      }
    ]
  }
  return option
}

/**
 * @param chartTitle
 * @param dataSeriesTitle
 * @param data [{name:a,value:1},{name:a,value:2}]
 * @returns {{yAxis: {splitLine: {show: boolean}, type: string, boundaryGap: *[]}, xAxis: {splitLine: {show: boolean}, type: string}, series: {hoverAnimation: boolean, showSymbol: boolean, data: *, name: *, type: string}[], tooltip: {formatter: (function(*): *), axisPointer: {animation: boolean}, trigger: string}, title: {text: *}}}
 */
export function getChartOptionSimpleLine(chartTitle, dataSeriesTitle, data) {
  const xAxisTitles = []
  data.map((v, k) => {
    xAxisTitles.push(v.name)
  })
  return {
    // 定义图表区与边缘的空白距离：左边距，右边距，下边距
    grid: {
      left: '5px',
      right: '5px',
      bottom: '5px',
      top: '25px',
      containLabel: true
    },
    title: {
      text: chartTitle,
      textStyle: {
        fontSize: '12px'
      }
    },
    tooltip: {
      trigger: 'axis',
      formatter: function(dataSeries) {
        // console.log('dataSeriesdataSeriesdataSeriesdataSeries', dataSeries)
        const itemOfData = dataSeries[0]
        return itemOfData.name + ' : ' + (itemOfData.value) + 'A'
        // return (itemOfData.value) + 'A'
      },
      axisPointer: {
        animation: false
      }
    },
    xAxis: {
      type: 'category',
      data: xAxisTitles
    },
    yAxis: {
      type: 'value',
      boundaryGap: [0, '100%'],
      splitLine: {
        show: false
      }
    },
    series: [{
      name: dataSeriesTitle,
      type: 'line',
      showSymbol: false,
      hoverAnimation: false,
      data: data
    }]
  }
}
