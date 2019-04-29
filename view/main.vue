<template>
  <main class="mdl-layout__content" id="main">
    <div class="page-content" id="page-content" @scroll.passive="scrolling">
      <d-article
        v-for="post in filterPosts"
        :key="post.id"
        :comment="status.comment"
        :listed="status.listed"
        :post="post"
      ></d-article>
      <div class="defaultblock" v-if="end.enabled">
        <div>
          <h4 style="color:white">
            <hr>
          </h4>
        </div>
        <div class="endBlock">{{end.label}}</div>
      </div>
    </div>
    <div
      class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active loadbar"
      :style="{display:loading?'block':'none'}"
    ></div>
    <button
      :disabled="!button"
      id="loadmore"
      @click="loadMore"
      class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--primary loadmoreButton"
    >
      <i class="material-icons">keyboard_arrow_down</i>
    </button>
  </main>
</template>
<script>
const article = require("./components/article.vue");
module.exports = {
  name: "d-main",
  props: ["status"],
  components: {
    "d-article": article
  },
  data() {
    return {
      loading: true,
      button: false,
      postLimit: 10,
      posts: [],
      end: {
        enabled: false,
        label: "沒有更多文章了"
      }
    };
  },
  computed: {
    filterPosts() {
      return this.status.pin
        ? this.posts
        : this.posts.filter(post => {
            return !post.pinned;
          });
    }
  },
  created() {
    this.selectForum();
  },
  watch: {
    //監控板塊變更
    "$route.params.forum": function() {
      this.selectForum();
    },
    //監控熱門最新變更
    "status.popular": function() {
      this.selectForum();
    },
    //是否開啟留言
    "status.comment": function() {
      this.selectForum();
    }
  },
  methods: {
    scrolling() {
      let main = document.getElementById("page-content");
      let offset = main.scrollTop + window.innerHeight;
      let height = main.scrollHeight;
      if (offset >= height - 150 && this.button) this.loadMore();
    },
    //切換板塊
    selectForum() {
      let forum = this.$route.params.forum;
      this.clear(); //清空文章
      this.getPostsImages();
    },
    //讀取更多
    loadMore() {
      this.getOlderPostsImages();
    },
    //清空文章
    clear() {
      this.posts = [];
      this.end.enabled = false;
    },
    //取得文章
    getPostsImages() {
      this.loading = true; //表示更新動畫
      this.button = false; //禁止按鈕
      let forum = this.$route.params.forum;
      let api = `https://www.dcard.tw/_api/forums/${forum}/posts?limit=${
        this.postLimit
      }&popular=${this.status.popular}`;
      if (forum == "all" || forum == undefined)
        api = `https://www.dcard.tw/_api/posts?limit=${
          this.postLimit
        }&popular=${this.status.popular}`;
      axios
        .get(api)
        .then(res => {
          res.data.map(p => {
            p.media.map(image => {
              image.thumbnail = (image.url.indexOf(`imgur`) > -1
                ? image.url.replace(`.jpg`, `m.jpg`)
                : image.url
              )
                .replace(`https`, `http`)
                .replace(`http`, `https`);
                
              if (image.url.indexOf("vivid.dcard.tw") > -1) {
                image.isVideo = true;
              }
            });
            this.posts.push(p);
          });
          this.loading = false; //結束更新動畫
          this.button = true; //停用按鈕
        })
        .catch(err => {
          console.log("取得所有文章失敗", err);
        });
    },
    //取得往前所有文章
    getOlderPostsImages() {
      this.loading = true; //表示更新動畫
      this.button = false; //禁止按鈕
      let lastId =
        this.posts.length > 0 ? this.posts[this.posts.length - 1].id : 0;
      let forum = this.$route.params.forum;
      let api = `https://www.dcard.tw/_api/forums/${forum}/posts?limit=${
        this.postLimit
      }&popular=${this.status.popular}&&before=${lastId}`;
      if (forum == "all" || forum == undefined)
        api = `https://www.dcard.tw/_api/posts?limit=${
          this.postLimit
        }&popular=${this.status.popular}&&before=${lastId}`;
      if (lastId > 0)
        axios
          .get(api)
          .then(res => {
            res.data.map(p => {
              p.media.map(image => {
                image.thumbnail = (image.url.indexOf(`imgur`) > -1
                  ? image.url.replace(`.jpg`, `m.jpg`)
                  : image.url
                )
                  .replace(`https`, `http`)
                  .replace(`http`, `https`);
                
                if (image.url.indexOf("vivid.dcard.tw") > -1) {
                  image.isVideo = true;
                }
              });
              this.posts.push(p);
            });
            this.loading = false; //結束更新動畫
            this.button = true; //啟用按鈕
            if (lastId == this.posts[this.posts.length - 1].id) {
              this.button = false; //停用按鈕
              this.end.enabled = true;
            }
          })
          .catch(err => {
            console.log(`取得${lastId}前文章失敗`, err);
          });
      else {
        this.loading = false; //結束更新動畫
        this.button = false; //停用按鈕
        this.end.enabled = true; //顯示結束區塊
      }
    }
  }
};
</script>

