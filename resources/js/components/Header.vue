<template>
  <div class="header">
    <h1>Online calculator</h1>
    <h3>Number in storage: {{ last || result }}</h3>
  </div>
</template>

<script>
export default {
  props: ['last', 'result']
}
</script>

<style scoped>
  .header {
  border: 2px dotted lightblue;
  background-color: white;
  padding: 10px;
  text-align: center;
  opacity: 0.8;
  }
  .header > * {
    opacity: 1;
    color: red;
  }
  .header:hover {
    opacity: 1;
    box-shadow: 5px 5px 5px red;
    background-color: lightcyan;
  }
</style>
