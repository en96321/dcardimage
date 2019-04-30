const master = require('../view/master.vue');

const routes = [
  {path:'/',redirect: '/all'},
  { path: '/:forum' },
  { path: '/:forum/:search' }
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


