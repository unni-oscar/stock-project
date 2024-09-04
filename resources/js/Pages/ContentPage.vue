<template>
  <ContentDelivery 
  :content="content"
    :symbol-details="symbolDetails"
    :data-as-on="dataAsOn"
    :datas="datas"
    :initial-date="selectedDate"
    :initial-sort-by="sortBy"
    @update="fetchReport"
     />
</template>


<script>
import ContentDelivery from '../components/ContentDelivery.vue'; // Ensure the path is correct

export default {
  props: {
    content: String,
    dataAsOn: String,
    selectedDate: String,
    sortBy: String,
    symbolDetails: Object,
    datas: Array

  },
  components: {
    ContentDelivery
  },
  methods: {
    fetchReport({ date, sortBy }) {
      this.$inertia.get(this.route('showReport'), { date, sort_by: sortBy });
      //this.$inertia.get(this.route('showReport'), { date, sort_by: sortBy });
    }
  }
}
</script>

<style scoped>
/* Add any specific styles here */
</style>

