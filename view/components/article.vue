<template>
  <div class="defaultblock" v-if="post.media.length>0">
    <div v-if="listed">
      <h4 class="text-white">
        {{post.title}}
        <span class="mdl-chip" style="vertical-align: middle;" v-if="post.pptcc">
          <span class="mdl-chip__text">ptt.cc</span>
        </span>
        <span class="mdl-chip" style="vertical-align: middle;" v-if="post.viemo">
          <span class="mdl-chip__text">vimeo</span>
        </span>
        <span class="mdl-chip" style="vertical-align: middle;" v-if="downloading.status">
          <span class="mdl-chip__text">壓縮中({{downloading.success}}/{{downloading.max}})</span>
        </span>
        <button v-else class="mdl-chip" style="vertical-align: middle;" @click="download">
          <span class="mdl-chip__text">下載本文圖片</span>
        </button>
      </h4>
    </div>
    <div :style="style">
      <template v-for="(image, index) in post.media">
        <div class="imgBlock mdl-card" v-if="image.isVideo" :key="post.id+'-'+index">
          <div class="mdl-card__title mdl-card--expand" style="padding:0px">
            <video class="videoBlock" controls>
              <source :src="image.url.replace('thumbnail.jpg','source')" type="video/mp4">
            </video>
          </div>
          <div class="mdl-card__actions text-white">
            <span v-if="!listed">{{post.title}}</span>
            <span v-if="image.floor!=undefined">#B{{image.floor}}</span>
            <span v-if="image.isVideo">#影片</span>
          </div>
        </div>
        <a
          :href="'https://www.dcard.tw/f/'+post.forumAlias+'/p/'+post.id"
          target="_blank"
          :key="post.id+'-'+index"
          :title="post.title"
          v-else
        >
          <div
            class="imgBlock mdl-card"
            :style="{'background': 'url(&quot;'+image.thumbnail+'&quot;) center center / cover'}"
          >
            <div class="mdl-card__title mdl-card--expand"></div>
            <div :class="{'mdl-card__actions':!listed||image.floor!=undefined||image.isVideo}">
              <span v-if="!listed">{{post.title}}</span>
              <span v-if="image.floor!=undefined">#B{{image.floor}}</span>
              <span v-if="image.isVideo">影片縮圖(前往連結觀賞)</span>
            </div>
          </div>
        </a>
      </template>
    </div>
  </div>
</template>
<script>
module.exports = {
  name: "d-article",
  props: ["comment", "listed", "post"],
  data() {
    return {
      downloading: {
        status: false,
        success: 0,
        max: 0
      },
      commentLimit: 100
    };
  },
  computed: {
    icon() {
      return this.status.listed ? "view_stream" : "view_module";
    },
    style() {
      return this.listed
        ? { display: "inline-block", width: "100%", height: "auto" }
        : {};
    }
  },
  mounted() {
    if (this.comment) this.getCommentsImages(this.post, 0);
  },
  methods: {
    //讀取dcard一般留言
    getCommentsImages(post, floor) {
      let api = `https://dcardimage.azurewebsites.net/comments.php?id=${post.id}&limit=${
        this.commentLimit
      }&after=${floor}`;
      axios
        .get(api)
        .then(res => {
          if (res.status == 200) {
            res.data.map(c => {
              if (c.content != null) {
                if (c.content.indexOf("ppt.cc") > -1) this.post.pptcc = true;
                if (c.content.indexOf("vimeo.com") > -1) this.post.vimeo = true;
              }
              c.mediaMeta.map(el => {
                if (el.url.indexOf(`vivid.dcard.tw`) > -1) {
                  el.isVideo = true;
                  el.thumbnail = el.url;
                  this.post.media.push(el);
                }
                if (el.type == "image/imgur") {
                  el.floor = c.floor;
                  el.thumbnail = el.url
                    .replace(`.jpg`, `m.jpg`)
                    .replace(`https`, `http`)
                    .replace(`http`, `https`);
                  this.post.media.push(el);
                }
              });
            });
            floor += this.commentLimit;
            if (floor < post.commentCount) this.getCommentsImages(post, floor);
          }
        })
        .catch(err => {
          console.log(`文章${post.id}-B${floor}後留言無法讀取`, err);
        });
    },
    //下載圖片
    download() {
      this.downloading.status = true;
      this.downloading.success = 0;
      let zip = new JSZip();
      let getImages = [];
      let datas = [];
      this.post.media.map(image => {
        if (image.url.indexOf("vivid.dcard.tw") < 0)
          getImages.push(
            axios
              .get(
                image.url.replace(`https`, `http`).replace(`http`, `https`),
                {
                  responseType: "arraybuffer"
                }
              )
              .then(res => {
                this.downloading.success++;
                datas.push(res);
              })
          );
      });
      this.downloading.max = getImages.length;
      axios
        .all(getImages)
        .then(res => {
          datas.map((image, index) => {
            zip.file(index + ".jpg", image.data);
          });
          for (i = 0; i < res.length; i++) {}
          zip.generateAsync({ type: "blob" }).then(content => {
            this.downloading.status = false;
            saveAs(content, this.post.id + ".zip");
          });
        })
        .catch(err => {
          this.downloading.status = false;
        });
    }
  }
};
</script>

