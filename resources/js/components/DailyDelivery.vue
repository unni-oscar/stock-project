<template>
  <div class="container">
    -->
    <h1>Stock Detail</h1>
    
    <!-- Sorting Form -->
    <form @submit.prevent="submitForm">
      <label for="datepicker">Select Date:</label>
      <!-- <input type="text" id="datepicker" v-model="date" ref="datepicker"> -->
       <datepicker v-model="date" format="DD-MM-YYYY" 
        ></datepicker>
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

    <p v-if="isEmpty(localSymbolDetails)">No symbols found.s</p>

    <table v-if="!isEmpty(localSymbolDetails)" border="1" cellspacing="0" cellpadding="5">
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
        <tr v-for="details in localSymbolDetails" :key="details.symbol" :style="getRowStyle(details)">
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
//import $ from 'jquery';
//import 'jquery-ui/ui/widgets/datepicker';
import Datepicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css'; // Import the styles

export default {
  components: {
    Datepicker
  },
  props: {
    symbolDetails: {
      type: Array,
      required: true
    },
   
    dataAsOn: {
      type: String,
      required: true
    },
    initialDate: {
      type: String,
      required: false,
      default: ''
    },
    initialSortBy: {
      type: String,
      required: false,
      default: 'latest_deliv_per'
    }
  },
  data() {
    return {
      date: this.initialDate,
      sortBy: this.initialSortBy,
      localSymbolDetails: this.symbolDetails
    };
  },
   formattedDate() {
      if (this.date) {
        return format(new Date(this.date), 'dd-MM-yyyy');
      }
      return '';
    },
  mounted() {
    //this.initializeDatePicker();
  },
  watch: {
    symbolDetails: {
      immediate: true,
      handler(newValue) {
        this.localSymbolDetails = newValue;
      }
    }
  },
  methods: {
    generateData() {
      this.$emit('updateDatas', { date: this.date, sortBy: this.sortBy });
      // Logic to re-fetch or generate data
      // This example assumes the data is static and provided via props
      // You can modify this if you want to implement data re-fetching
    },
    isEmpty(obj) {
        
      return Object.keys(obj).length === 0;
    },
    initializeDatePicker() {
      const self = this;
      $(this.$refs.datepicker).datepicker({
        dateFormat: 'dd-mm-yy',
        defaultDate: this.date,
        onSelect(dateText) {
          self.date = dateText;
          self.submitForm();
        }
      });
    },
    submitForm() {            
      this.$emit('update', { date: this.date, sortBy: this.sortBy });
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
    formatNumber(value) {
      return Number(value).toFixed(2);
    }
  }
};
</script>

<style>
/* Add your styles here */
</style>
