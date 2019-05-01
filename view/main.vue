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
      @click="showHelp"
      class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--primary loadmoreButton"
      style="margin-bottom: 64px;background-color:rgba(196,206,52,1)"
    >
      <i class="material-icons">attach_money</i>
    </button>
    <button
      :disabled="!button"
      id="loadmore"
      @click="loadMore"
      class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--primary loadmoreButton"
    >
      <i class="material-icons">keyboard_arrow_down</i>
    </button>
    <dialog class="mdl-dialog" id="help" style="width: calc(100% - 100px);max-width:600px">
      <h4 class="mdl-dialog__title">
        支持作者
        <a href="https://p.ecpay.com.tw/FDD74">
          <img src="https://payment.ecpay.com.tw/Content/themes/WebStyle20170517/images/ecgo.png">
        </a>
      </h4>
      <div class="mdl-dialog__content">
        <div class="mdl-grid">
          <p>贊助商</p>
          <a href="https://taiwancoldnews.com" target="_blank">
            <img style="max-width:100%" src="assets/images/tcnews.png">
          </a>
        </div>
        <p>透過點擊廣告幫助作者維護此網站</p>
        <div class="mdl-grid">
          <div class="mdl-cell mdl-cell--6-col">
            <iframe
              src="//ads.exosrv.com/iframe.php?idzone=3373751&size=300x250"
              width="300"
              height="250"
              scrolling="no"
              marginwidth="0"
              marginheight="0"
              frameborder="0"
            ></iframe>
          </div>
          <div class="mdl-cell mdl-cell--6-col">
            <iframe
              src="//ads.exosrv.com/iframe.php?idzone=3374821&size=300x250"
              width="300"
              height="250"
              scrolling="no"
              marginwidth="0"
              marginheight="0"
              frameborder="0"
            ></iframe>
          </div>
        </div>
      </div>
      <div class="mdl-dialog__actions">
        <button type="button" class="mdl-button close">關閉</button>
      </div>
    </dialog>
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
      },
      searchBaseAPI: "https://dcardimage.000webhostapp.com/search.php",
      defaultBaseAPI: "https://www.dcard.tw/_api"
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
    this.search();
  },
  watch: {
    "$route.params.search": function() {
      this.search();
    },
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
    showHelp() {
      var help = document.querySelector("#help");
      if (!help.showModal) {
        dialogPolyfill.registerDialog(help);
      }
      help.querySelector(".close").addEventListener("click", function() {
        help.close();
      });
      help.showModal();
    },
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
      if (this.$route.params.search == undefined) this.getOlderPostsImages();
      else this.searchOlderPostsImages();
    },
    //清空文章
    clear() {
      this.posts = [];
      this.end.enabled = false;
    },
    search() {
      if (this.$route.params.search != undefined) {
        this.searchPostsImages();
      } else this.selectForum();
    },
    //搜尋文章
    searchPostsImages() {
      this.clear(); //清空文章
      this.loading = true; //表示更新動畫
      this.button = false; //禁止按鈕
      let forum = this.$route.params.forum;
      let api = `${this.searchBaseAPI}?limit=${this.postLimit}&since=0&query=${
        this.$route.params.search
      }&offset=0`;
      if (forum != undefined && forum != "all")
        api = `${this.searchBaseAPI}?limit=${this.postLimit}&since=0&query=${
          this.$route.params.search
        }&offset=0&forum=${forum}`;
      axios
        .get(api)
        .then(res => {
          res.data.map(p => {
            p.media.map(image => {
              image.thumbnail = (image.url.indexOf(`imgur`) > -1
                ? image.url.replace(`.jpg`, `m.jpg`).replace(`.png`, `m.jpg`)
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
          console.log("搜尋文章失敗", err);
        });
    },
    //搜尋往前所有文章
    searchOlderPostsImages() {
      this.loading = true; //表示更新動畫
      this.button = false; //禁止按鈕
      let forum = this.$route.params.forum;
      let api = `${this.searchBaseAPI}?limit=${this.postLimit}&since=0&query=${
        this.$route.params.search
      }&offset=${this.posts.length}`;
      if (forum != undefined && forum != "all")
        api = `${this.searchBaseAPI}?limit=${this.postLimit}&since=0&query=${
          this.$route.params.search
        }&offset=${this.posts.length}&forum=${forum}`;
      axios
        .get(api)
        .then(res => {
          if (res.data.length == 0) {
            this.loading = false; //結束更新動畫
            this.button = false; //啟用按鈕
          } else {
            res.data.map(p => {
              p.media.map(image => {
                image.thumbnail = (image.url.indexOf(`imgur`) > -1
                  ? image.url.replace(`.jpg`, `m.jpg`).replace(`.png`, `m.jpg`)
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
          }
        })
        .catch(err => {
          console.log(`取得${lastId}前文章失敗`, err);
        });
    },
    //取得文章
    getPostsImages() {
      this.loading = true; //表示更新動畫
      this.button = false; //禁止按鈕
      let forum = this.$route.params.forum;
      let api = `${this.defaultBaseAPI}/forums/${forum}/posts?limit=${
        this.postLimit
      }&popular=${this.status.popular}`;
      if (forum == "all" || forum == undefined)
        api = `${this.defaultBaseAPI}/posts?limit=${this.postLimit}&popular=${
          this.status.popular
        }`;
      axios
        .get(api)
        .then(res => {
          res.data.map(p => {
            p.media.map(image => {
              image.thumbnail = (image.url.indexOf(`imgur`) > -1
                ? image.url.replace(`.jpg`, `m.jpg`).replace(`.png`, `m.jpg`)
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
      let api = `${this.defaultBaseAPI}/forums/${forum}/posts?limit=${
        this.postLimit
      }&popular=${this.status.popular}&&before=${lastId}`;
      if (forum == "all" || forum == undefined)
        api = `${this.defaultBaseAPI}/posts?limit=${this.postLimit}&popular=${
          this.status.popular
        }&&before=${lastId}`;
      if (lastId > 0)
        axios
          .get(api)
          .then(res => {
            res.data.map(p => {
              p.media.map(image => {
                image.thumbnail = (image.url.indexOf(`imgur`) > -1
                  ? image.url.replace(`.jpg`, `m.jpg`).replace(`.png`, `m.jpg`)
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

