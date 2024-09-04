<!-- resources/js/App.vue -->
<template>
  <InertiaApp :initial-page="initialPage" :resolveComponent="resolveComponent" />
</template>

<script>
import { InertiaApp } from '@inertiajs/inertia-vue3';
import { createApp, h } from 'vue';

export default {
  name: 'App',
  components: {
    InertiaApp
  },
  setup() {
    const resolveComponent = (name) => {
      return import(`./Pages/${name}`).then(module => module.default);
    };

    return {
      resolveComponent
    };
  }
};
</script>
