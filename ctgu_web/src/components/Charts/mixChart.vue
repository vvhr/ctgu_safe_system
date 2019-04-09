<template>
  <div :class="className" :id="id" :style="{height:height,width:width}">
    <div style="font-size: 18px;color: #ffe215;margin: 100px 41vw;width: 100%;position: relative;">如果监控图表不显示,请刷新本页面!</div>
  </div>
</template>

<script>
  import resize from './mixins/resize'

  export default {
    mixins: [resize],
    props: {
      className: {
        type: String,
        default: 'chart'
      },
      id: {
        type: String,
        default: 'chart'
      },
      width: {
        type: String,
        default: '200px'
      },
      height: {
        type: String,
        default: '200px'
      },
      reportData: {
        type: Object
      },
      uuid: {
        type: String
      },
      flag_watched_by_chart: {
        type: Number
      }
    },
    data() {
      return {
        // 折线图实例
        chart: null,
        data: this.reportData
      }
    },
    mounted() {
      console.log('初始化图表')
      this.initChart()
      this.data = this.reportData
      // setInterval(this.test, 2000)
    },
    beforeDestroy() {
      if (!this.chart) {
        return
      }
      this.chart.dispose()
      this.chart = null
    },
    watch: {
      flag_watched_by_chart(value, oldValue) {
        // console.log('数据变化', this.data.time_arr)
        console.log('flag_watched_by_chart', value, oldValue)
        this.chart.setOption({
          backgroundColor: 'rgba(0, 104, 144, 0.45)',
          title: {
            text: '功率变化监测图',
            x: '20',
            top: '15',
            textStyle: {
              color: '#fff',
              fontSize: '15'
            },
            subtextStyle: {
              color: '#90979c',
              fontSize: '18'
            }
          },
          tooltip: {
            trigger: 'axis',
            axisPointer: {
              textStyle: {
                color: '#fff'
              }
            }
          },
          grid: {
            left: '5%',
            right: '5%',
            borderWidth: 0,
            top: 100,
            bottom: 55,
            textStyle: {
              color: '#fff'
            }
          },
          legend: {
            x: '5%',
            top: '10%',
            textStyle: {
              color: '#90979c'
            },
            data: ['current(A*100)', 'voltage(V)', 'power(W)']
          },
          calculable: true,
          xAxis: [{
            type: 'category',
            axisLine: {
              lineStyle: {
                color: '#90979c'
              }
            },
            splitLine: {
              show: false
            },
            axisTick: {
              show: false
            },
            splitArea: {
              show: false
            },
            axisLabel: {
              interval: 0

            },
            data: this.reportData.time_arr // 存放x轴
          }],
          yAxis: [{
            type: 'value',
            splitLine: {
              show: false
            },
            // 显示刻度线
            axisLine: {
              lineStyle: {
                color: '#90979c'
              }
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              interval: 0
            },
            splitArea: {
              show: false
            }
          }],
          series: [{
            name: 'current(A*100)',
            type: 'bar',
            stack: 'total',
            barMaxWidth: 35,
            barGap: '10%',
            itemStyle: {
              normal: {
                color: 'rgba(255,144,128,1)',
                label: {
                  show: true,
                  textStyle: {
                    color: '#fff'
                  },
                  position: 'insideTop',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.reportData.current_arr
          },
          {
            name: 'voltage(V)',
            type: 'bar',
            stack: 'total',
            itemStyle: {
              normal: {
                color: 'rgba(0,191,183,1)',
                barBorderRadius: 0,
                label: {
                  show: true,
                  position: 'top',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.reportData.voltage_arr
          }, {
            name: 'power(W)',
            type: 'line',
            stack: 'total',
            symbolSize: 10,
            symbol: 'circle',
            itemStyle: {
              normal: {
                color: 'rgba(252,230,48,1)',
                barBorderRadius: 0,
                label: {
                  show: true,
                  position: 'top',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.reportData.power_arr
          }]
        })
      },
      uuid: function(value, oldValue) {
        // console.log('UUID变化', this.uuid)
        this.data = this.reportData
      }
    },
    methods: {
      test() {
        console.log(this.reportData)
      },
      initChart() {
        // 初始化实例
        this.chart = this.$echarts.init(document.getElementById(this.id))
        // 图表绘制
        this.chart.setOption({
          backgroundColor: 'rgba(0, 104, 144, 0.45)',
          title: {
            text: '功率变化监测图',
            x: '20',
            top: '15',
            textStyle: {
              color: '#fff',
              fontSize: '15'
            },
            subtextStyle: {
              color: '#90979c',
              fontSize: '18'
            }
          },
          tooltip: {
            trigger: 'axis',
            axisPointer: {
              textStyle: {
                color: '#fff'
              }
            }
          },
          grid: {
            left: '5%',
            right: '5%',
            borderWidth: 0,
            top: 100,
            bottom: 55,
            textStyle: {
              color: '#fff'
            }
          },
          legend: {
            x: '5%',
            top: '10%',
            textStyle: {
              color: '#90979c'
            },
            data: ['current(A*100)', 'voltage(V)', 'power(W)']
          },
          calculable: true,
          xAxis: [{
            type: 'category',
            axisLine: {
              lineStyle: {
                color: '#90979c'
              }
            },
            splitLine: {
              show: false
            },
            axisTick: {
              show: false
            },
            splitArea: {
              show: false
            },
            axisLabel: {
              interval: 0

            },
            data: this.data.time_arr // 存放x轴
          }],
          yAxis: [{
            type: 'value',
            splitLine: {
              show: false
            },
            // 显示刻度线
            axisLine: {
              lineStyle: {
                color: '#90979c'
              }
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              interval: 0
            },
            splitArea: {
              show: false
            }
          }],
          series: [{
            name: 'current(A*100)',
            type: 'bar',
            stack: 'total',
            barMaxWidth: 35,
            barGap: '10%',
            itemStyle: {
              normal: {
                color: 'rgba(255,144,128,1)',
                label: {
                  show: true,
                  textStyle: {
                    color: '#fff'
                  },
                  position: 'insideTop',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.reportData.current_arr
          },
          {
            name: 'voltage(V)',
            type: 'bar',
            stack: 'total',
            itemStyle: {
              normal: {
                color: 'rgba(0,191,183,1)',
                barBorderRadius: 0,
                label: {
                  show: true,
                  position: 'top',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.data.voltage_arr
          }, {
            name: 'power(W)',
            type: 'line',
            stack: 'total',
            symbolSize: 10,
            symbol: 'circle',
            itemStyle: {
              normal: {
                color: 'rgba(252,230,48,1)',
                barBorderRadius: 0,
                label: {
                  show: true,
                  position: 'top',
                  formatter(p) {
                    return p.value > 0 ? p.value : ''
                  }
                }
              }
            },
            data: this.data.power_arr
          }
          ]
        })
      }
    }
  }
</script>
