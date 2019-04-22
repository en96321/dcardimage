const master = require('../view/master.vue');

const routes = [
  { path: '/:forum/:popular' },
  { path: '/:forum' }
  
];
const router = new VueRouter({
  routes
})
const app = new Vue({
  el:'#app',
  router,
  components: {
    'master': master
}
  
});


