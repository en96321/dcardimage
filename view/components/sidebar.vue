<template>
    <div >
      <div class="mdl-chip mdl-chip--contact mdl-chip--deletable" id="searchBox">
        <span class="mdl-chip__contact mdl-color--primary mdl-color-text--white">
          <i class="material-icons">search</i>
        </span>
        <span class="mdl-chip__text">
          <input class="mdl-textfield__input" type="text" id="search" v-model="search">
        </span>
      </div>
      <div style="overflow-x:scroll;display: inline-flex;margin-bottom: -14px;padding-bottom: 5px;width:100%">
        <router-link
          v-for="(item, index) in defaultForums"
          class="mdl-button mdl-js-button mdl-button mdl-js-ripple-effect speedDial mdl-color-text--primary"
          :key="index"
          :to="'/'+item.alias"
        >#{{item.label}}</router-link>
      </div>
      <hr>
      <nav class="mdl-navigation" id="nav">
        <div
          class="mdl-progress mdl-js-progress mdl-progress__indeterminate"
          style="margin-top: -16px"
          :style="{display:forum.status=='loading'?'block':'none'}"
        ></div>
        <template v-if="forum.status=='loading'"></template>
        <template v-else-if="forum.status=='error'">
          <button
            class="mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-button--colored"
            style="margin: 0px auto;"
            @click="getForums"
          >
            <i class="material-icons">refresh</i>
          </button>
        </template>
        <template v-else>
          <router-link
            v-for="(forum, index) in forums"
            :key="index"
            class="mdl-navigation__link"
            :to="'/'+forum.alias"
          >
            {{forum.name}}
            <i class="material-icons miniIcon" v-if="forum.isSchool">school</i>
            <i class="material-icons miniIcon" v-if="forum.invisible">visibility_off</i>
          </router-link>
        </template>
      </nav>
    </div>
</template>
<script>
module.exports = {
  name: "d-siadebar",
  data() {
    return {
      search: "",
      forum: {
        status: "loading",
        lists: [
          { alias: "all", name: "全部", invisible: false, isSchool: false }
        ]
      },
      defaultForums: [
        {
          label: "穿搭",
          alias: "dressup"
        },
        {
          label: "美妝",
          alias: "makeup"
        },
        {
          label: "西斯",
          alias: "sex"
        },
        {
          label: "攝影",
          alias: "photography"
        },
        {
          label: "美食",
          alias: "food"
        }
      ]
    };
  },
  watch: {
    "$route.params.forum": function() {
      this.$emit('changeTitle',this.title);
    }
  },
  computed: {
    title() {
      let f = this.forum.lists.find(list => {
        return list.alias == this.$route.params.forum;
      });
      return f == undefined ? "全部" : f.name;
    },
    forums() {
      return this.search == ""
        ? this.forum.lists
        : this.forum.lists.filter(val => {
            return (
              val.alias.indexOf(this.search) > -1 ||
              val.name.indexOf(this.search) > -1
            );
          });
    }
  },
  created() {
    this.getForums();
  },
  methods: {
    getForums() {
      this.forum.status = "loading";
      this.forum.lists = [
        { alias: "all", name: "全部", invisible: false, isSchool: false }
      ];
      axios
        .get("https://www.dcard.tw/_api/forums/")
        .then(res => {
          this.forum.status = "success";
          this.forum.lists = this.forum.lists.concat(res.data);
           this.$emit('changeTitle',this.title);
        })
        .catch(error => {
          this.forum.status = "error";
        });
    }
  }
};
</script>

