<template>
  <div>
    <Header :last="previous" :result="result"/>
    <div class="calculator">
      <div class="result">{{ result || 0 }}</div>
      <div class="btn" @click="clear">AC</div>
      <div class="btn" @click="prefix">+/-</div>
      <div class="btn" @click="percent">%</div>
      <div class="btn operator" @click="divide">/</div>
      <div class="btn" @click="addNumber(7)">7</div>
      <div class="btn" @click="addNumber(8)">8</div>
      <div class="btn" @click="addNumber(9)">9</div>
      <div class="btn operator" @click="multiply">*</div>
      <div class="btn" @click="addNumber(4)">4</div>
      <div class="btn" @click="addNumber(5)">5</div>
      <div class="btn" @click="addNumber(6)">6</div>
      <div class="btn operator" @click="substract">-</div>
      <div class="btn" @click="addNumber(1)">1</div>
      <div class="btn" @click="addNumber(2)">2</div>
      <div class="btn" @click="addNumber(3)">3</div>
      <div class="btn operator" @click="add">+</div>
      <div class="btn zero" @click="addNumber(0)">0</div>
      <div class="btn" @click="dot">.</div>
      <div class="btn operator" @click="equal">=</div>
    </div>
  </div>
</template>

<script>
import Header from '../components/Header'
export default {
  name: 'App',
  components: {
    Header
  },
  data() {
    return {
      result: '',
      operator: null,
      previous: null,
      operatorClicked: false
    }
  },
  methods: {
    addNumber(num) {
      if(this.result.length >= 20) return
      if(this.result == '0' && num == '0') return
      
      if(this.operatorClicked) {
        this.result = ''
        this.operatorClicked = false
      }
      this.result = `${this.result}${num}`
    },
    clear() {
      this.result = ''
    },
    prefix() {
      this.result = this.result.charAt(0) === '-' ? this.result.replace('-', '') : `-${this.result}`
    },
    percent() {
      this.result = `${parseFloat(this.result) / 100}`
    },
    dot() {
      this.result = !this.result.includes('.') ? `${this.result}.` : this.result
    },
    add() {
      if(this.previous && this.operator) {this.equal()}
      this.setData()
      this.operator = (a, b) => a + b
    },
    substract() {
      if(this.previous && this.operator) {this.equal()}
      this.setData()
      this.operator = (a, b) => a - b
    },
    multiply() { 
      if(this.previous && this.operator) {this.equal()}
      this.setData()
      this.operator = (a, b) => a * b
    },
    divide() {
      if(this.previous && this.operator) {this.equal()}
      
      this.setData()
      this.operator = (a, b) => a / b
    },
    setData() {
      this.previous = this.result
      this.operatorClicked = true
    },
    equal() {
      if(!this.operator || !this.result || !this.previous) return
      this.result = `${this.operator(parseFloat(this.previous), parseFloat(this.result))}`
      this.previous = null
    }
  }
}
</script>

<style scoped>
body {
  background-image: url('https://storage.needpix.com/rsynced_images/matrix-1461373324WTC.jpg');  
}
.calculator {
  width: 500px;
  border: 2px solid black;
  margin: 20px auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: 50px;
  text-align: center;
  font-size: 35px;
}
.result {
  background-color: lightblue;
  grid-column: 1 / 5;
}
.btn {
  background-color: grey;
  border: 1px solid black;
}
.btn:hover {
  cursor: pointer;
  background-color: lightgrey;
}
.operator {
  background-color: orange;
}
.zero {
  grid-column: 1 / 3;
}
</style>