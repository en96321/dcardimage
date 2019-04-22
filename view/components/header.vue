<template>
    <header class="mdl-layout__header mdl-layout__header--transparent">
      <div class="mdl-layout__header-row">
        <span class="mdl-layout-title">{{title}}</span>
        <div class="mdl-layout-spacer"></div>
        <nav class="mdl-navigation" style="overflow: hidden;">
          <label
            class="mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect"
            for="popular"
            title="熱門"
          >
            <input
              type="checkbox"
              id="popular"
              class="mdl-icon-toggle__input"
              v-model="status.popular"
            >
            <i class="mdl-icon-toggle__label material-icons">whatshot</i>
          </label>
          <label
            class="mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect"
            for="pin"
            title="置頂文章"
          >
            <input type="checkbox" id="pin" class="mdl-icon-toggle__input" v-model="status.pin">
            <i class="mdl-icon-toggle__label material-icons">grade</i>
          </label>
          <label
            class="mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect"
            title="留言"
            for="comment"
          >
            <input
              type="checkbox"
              id="comment"
              class="mdl-icon-toggle__input"
              v-model="status.comment"
            >
            <i class="mdl-icon-toggle__label material-icons">comment</i>
          </label>
          <label
            class="mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect"
            title="模式"
            for="mode"
          >
            <input type="checkbox" id="mode" class="mdl-icon-toggle__input" v-model="status.listed">
            <i class="mdl-icon-toggle__label material-icons mdl-color-text--primary">{{icon}}</i>
          </label>
          <div class="mdl-navigation__link mdl-color--accent" style="margin-left:16px">
            <h6 style="font-weight:bolder">D卡圖片庫</h6>
          </div>
        </nav>
      </div>
    </header>
</template>
<script>
module.exports = {
  name: "d-header",
  props:['status','title'],
  computed: {
    icon() {
      return this.status.listed ? "view_stream" : "view_module";
    }
  },
};
</script>

