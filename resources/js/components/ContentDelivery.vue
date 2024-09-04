<template>

  <div class="container">
     <h2>Content Delivery</h2>
    <p>{{ content }}</p>
    <h1>Stock Details</h1>
    
    <!-- Sorting Form -->
   
    <form @submit.prevent="submitForm">
      <label for="datepicker">Select Date:</label>
      <!-- <input type="text" id="datepicker" v-model="date" ref="datepicker"> -->
       <!-- <datepicker v-model="date"></datepicker> -->
      <p>Selected Date: {{ date }}</p>
      <label for="sort_by">Sort by:</label>
      <select v-model="sortBy" id="sort_by" @change="submitForm">
        <option value="latest_deliv_per">Latest Delivery Percentage</option>
        <option value="three_day_avg">3-Day Average</option>
        <option value="five_day_avg">5-Day Average</option>
        <option value="thirty_day_avg">30-Day Average</option>
        <option value="highest_price_move">Highest Price Move</option>
        <option value="turnover_lacs">Turnover (Lacs)</option>
      </select>
      
      <label>Data as on: {{ dataAsOn }}</label>
    </form>
<p v-if="symbolDetails.length === 0">No symbols found.</p>
<p v-if="isEmpty(symbolDetails)">No symbols found.s</p>
<p >No symbols foundgfdg d.s</p>
    <table v-if="symbolDetails.length > 0" border="1" cellspacing="0" cellpadding="5">
      <thead>
        <tr>
          <th>Name</th>
          <th colspan="4">Delivery Percentages and Averages</th>
          <th>Highest Price Move</th>
          <th>Turnover (Lacs)</th>
        </tr>
        <tr>
          <th></th>
          <th>Today</th>
          <th>3-Day</th>
          <th>5-Day</th>
          <th>30-Day </th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="details in symbolDetails" :key="details.symbol" :style="getRowStyle(details)">
          <td>{{ details.symbol }}</td>
          <td>{{ formatNumber(details.latest_deliv_per) }}</td>
          <td>{{ formatNumber(details.three_day_avg) }}</td>
          <td>{{ formatNumber(details.five_day_avg) }}</td>
          <td>{{ formatNumber(details.thirty_day_avg) }}</td>
          <td>{{ formatNumber(details.highest_price_move) }}</td>
          <td>{{ formatNumber(details.turnover_lacs) }}</td>
        </tr>
      </tbody>
    </table>

   
  </div>
</template>


<script>
export default {
  props: {
    content: String,
    symbolDetails: {
      type: Object,
      default: () => ({})
    },
  },
  methods: {
    isEmpty(obj) {
        console.log(obj)
      return Object.keys(obj).length === 0;
    },
    formatNumber(value) {
      return Number(value).toFixed(2);
    },
    getRowStyle(details) {
      if (
        details.latest_deliv_per > details.three_day_avg &&
        details.three_day_avg > details.five_day_avg &&
        details.five_day_avg > details.thirty_day_avg
      ) {
        return { backgroundColor: '#d4edda' };
      }
      return {};
    },
    submitForm() {
      this.$emit('update', { date: this.date, sortBy: this.sortBy });
    },
  }
  
}
</script>

