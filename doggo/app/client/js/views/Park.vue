<template>
    <form enctype="multipart/form-data" novalidate>
        <div class="park" v-if="park" >
            <a href="/" @click.exact.prevent="$router.push('/')">Close</a>
            <h2>{{ park.Title }}</h2>
            <ul class="park__features">
                <li>{{ park.FeatureOnOffLeash }}</li>
            </ul>
            <p class="park__notes">{{ park.Notes }}</p>
            <p class="park__provider">Managed by <strong>{{ park.Provider }}</strong></p>
             <img v-bind:src="imagePath" />
            <h3>Upload an image of your dog at this park</h3>
            <label>File
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()" />
            </label>
            <button v-on:click="submitFile()">Submit</button>
        </div>
    </form>
</template>

<script>
import * as axios from 'axios';

export default {
  computed: {
    imagePath() {
      return this.park.PhotoURL;
    },
    park() {
      return this.$store.state.parks.find(park => park.ID === parseInt(this.$route.params.id, 10));
    },
    parks() {
      return this.$store.state.parks;
    },
  },
  methods: {
      submitFile() {
      let formData = new FormData();
      formData.append('file', this.file);
      formData.append('park', this.park.ID);
      // Custom Silverstripe REST API.
      axios.post('/api/v1/parks/$ID//imageupload',
        formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      )
    },
    handleFileUpload() {
      this.file = this.$refs.file.files[0];
    },
  },
}

</script>

<style>
  .park {
    padding: 20px;
  }
  img {
      width: 150px;
      border-radius: 2px;
      box-shadow: 1px 1px 3px 1px rgba(0, 0, 0, 0.5);
      transition: width 1s;
  }
</style>
