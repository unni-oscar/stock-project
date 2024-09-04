<!-- resources/js/Pages/DummyDataTable.vue -->

<template>
  <div>
    <h1>Dummy Data Table</h1>
    <button @click="generateData">Generate Data</button>
    <table v-if="data.length">
      <thead>
        <tr>
          <th>Index</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in data" :key="index">
          <td>{{ index + 1 }}</td>
          <td>{{ item }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    data: Array,
  },
  methods: {
    generateData() {
      // Logic to re-fetch or generate data
      // This example assumes the data is static and provided via props
      // You can modify this if you want to implement data re-fetching
    }
  }
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
  padding: 8px;
}
th {
  background-color: #f2f2f2;
}
</style>
