import Vue from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue';
// import { ZiggyVue } from 'ziggy';
// import { Ziggy } from  "./ziggy"

Vue.use(plugin);
// Vue.use(ZiggyVue, Ziggy);

const app = document.getElementById('app');

new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => require(`./Pages/${name}`).default,
        },
    }),
}).$mount(app);



// import Vue from 'vue';
// import { InertiaApp } from '@inertiajs/inertia-vue';
// import { InertiaProgress } from '@inertiajs/progress';

// // Configure Inertia
// Vue.use(InertiaApp);

// new Vue({
//   el: '#app',
//   render: h => h(InertiaApp, {
//     props: {
//       initialPage: JSON.parse(document.getElementById('app').dataset.page),
//       resolveComponent: name => require(`./Pages/${name}`).default,
//     },
//   }),
// });

// // Configure Inertia Progress
// InertiaProgress.init({
//   color: '#29d', // Progress bar color
//   showSpinner: true, // Show a spinner
// });





// import Vue from 'vue';
// import { InertiaApp } from '@inertiajs/inertia-vue';
// import { InertiaProgress } from '@inertiajs/progress';

// Vue.use(InertiaApp);

// const app = new Vue({
//   el: '#app',
//   render: h => h(InertiaApp, {
//     props: {
//       initialPage: JSON.parse(document.getElementById('app').dataset.page),
//       resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
//     }
//   })
// });

// InertiaProgress.init();

// import { createApp, h } from 'vue';
// import { createInertiaApp } from '@inertiajs/inertia-vue3';
// import { InertiaProgress } from '@inertiajs/progress';

// createInertiaApp({
//   resolve: name => import(`./Pages/${name}`),
//   setup({ el, App, props }) {
//     createApp({ render: () => h(App, props) }).mount(el);
//   },
// });

// InertiaProgress.init();
