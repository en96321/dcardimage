<template>
  <div class="defaultblock" v-if="post.media.length>0">
    <div v-if="listed">
      <h4 class="text-white">
        {{post.title}}
        <button
          v-if="loading"
          class="mdl-button mdl-js-button mdl-js-ripple-effect text-white"
          disabled="true"
        >壓縮中</button>
        <button
          v-else
          class="mdl-button mdl-js-button mdl-js-ripple-effect text-white"
          @click="download"
        >下載本文圖片</button>
      </h4>
    </div>
    <div :style="style">
      <a
        v-for="(image, index) in post.media"
        :key="post.id+'-'+index"
        :href="'https://www.dcard.tw/f/'+post.forumAlias+'/p/'+post.id"
        target="_blank"
        :title="post.title"
      >
        <div
          class="imgBlock mdl-card"
          :style="{'background': 'url(&quot;'+(image.url.indexOf(`imgur`)>-1?image.url.replace(`.jpg`,`m.jpg`):image.url).replace(`https`,`http`).replace(`http`,`https`)+'&quot;) center center / cover'}"
        >
          <div class="mdl-card__title mdl-card--expand"></div>
          <div :class="{'mdl-card__actions':!listed||image.floor!=undefined}">
            <span v-if="!listed">{{post.title}}</span>
            <span v-if="image.floor!=undefined">#B{{image.floor}}</span>
          </div>
        </div>
      </a>
    </div>
  </div>
</template>
<script>
module.exports = {
  name: "d-article",
  props: ["comment", "listed", "post"],
  data() {
    return {
      loading: false,
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
      let api = `https://www.dcard.tw/_api/posts/${post.id}/comments?limit=${
        this.commentLimit
      }&after=${floor}`;
      axios
        .get(api)
        .then(res => {
          if (res.status == 200)
            res.data.forEach(c => {
              c.mediaMeta
                .filter(img => {
                  return img.type == "image/imgur";
                })
                .map(el => {
                  el.floor = c.floor;
                  return el;
                })
                .forEach(el => {
                  this.post.media.push(el);
                });
            });
          floor += this.commentLimit;
          if (floor < post.commentCount) this.getCommentsImages(post, floor);
        })
        .catch(err => {
          console.log(`文章${post.id}-B${floor}後留言無法讀取`, err);
        });
    },
    //下載圖片
    download() {
      this.loading = true;
      let zip = new JSZip();
      let getImages = [];
      this.post.media.forEach(image => {
        getImages.push(
          axios.get(image.url, {
            responseType: "arraybuffer"
          })
        );
      });
      axios.all(getImages).then(res => {
        res.forEach((image, index) => {
          zip.file(index + ".jpg", image.data);
        });

        zip.generateAsync({ type: "blob" }).then(content => {
          this.loading = false;
          saveAs(content, this.post.id + ".zip");
        });
      });
    }
  }
};
</script>

