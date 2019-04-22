<template>
  <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header" id="app">
    <d-header :status="status" :title="title"></d-header>
    <div class="mdl-layout__drawer">
      <d-sidebar @changeTitle="title=$event"></d-sidebar>
    </div>
    <d-main :status="status"></d-main>
  </div>
</template>
<script>
const header = require("./components/header.vue");
const sidebar = require("./components/sidebar.vue");
const main = require("./main.vue");
module.exports = {
  name: "master",
  components: {
    "d-header": header,
    "d-sidebar": sidebar,
    "d-main": main
  },
  data() {
    return {
      title: "",
      status: {
        pin: false,
        popular: true,
        listed: true,
        comment: true
      }
    };
  }
};
</script>

