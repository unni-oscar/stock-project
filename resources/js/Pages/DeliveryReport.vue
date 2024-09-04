// Inertia Page (e.g., StockReport.vue)
<template>
  <DailyDelivery
    :symbol-details="symbolDetails"
    :data-as-on="dataAsOn"
    :datas= "datas"
    :initial-date="selectedDate"
    :initial-sort-by="sortBy"
    @update="fetchReport"
    @updateDatas="fetchReportData"
  />
</template>

<script>
import DailyDelivery from '../components/DailyDelivery.vue';

export default {
  components: {
    DailyDelivery
  },
  props: {
    symbolDetails: Array,
    dataAsOn: String,
    selectedDate: String,
    sortBy: String,
    datas: Array
  },
  methods: {
    fetchReport({ date, sortBy }) {
      console.log('Fetching report with:', { date, sortBy });
      this.$inertia.get('/DeliveryReport', { date, sort_by: sortBy }, {
        preserveState: true, // Keep the state if needed
        onSuccess: (page) => {
          console.log('Inertia response:', page.props.symbolDetails);         
          this.symbolDetails = page.props.symbolDetails;          
        }
      });      
    },
    fetchReportData({ date, sortBy }) {       
      this.$inertia.get('/DeliveryReport', { date, sort_by: sortBy }, {
        preserveState: true, // Keep the state if needed
        onSuccess: (page) => {          
          this.data = page.props.datas;          
        }
      });      
    }
  }
};
</script>
