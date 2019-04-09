<template>
  <div class="app-container">
    <h1>test</h1>
    <div style="width: 500px">
      <addressComponent v-model="address"></addressComponent>
    </div>
    <div>{{address}}</div>
  </div>
</template>
<script>
import addressComponent from '../../components/address'

export default {
  components: { addressComponent },
  data() {
    return {
      address: []
    }
  },
  mounted() {
    const promise = this.createPromise().then(data => {
      console.log(data)
      setTimeout(() => {
        console.log('setTimeOut:5000')
      }, 5000)
      return 2
    }).then(data => {
      console.log(data)
      return 3
    })
    setTimeout(() => {
      console.log(promise)
    }, 4000)
  },
  methods: {
    createPromise() {
      return new Promise(function(done, fail) {
        setTimeout(() => {
          done('1')
        }, 3000)
      })
    }
  }
}
</script>
