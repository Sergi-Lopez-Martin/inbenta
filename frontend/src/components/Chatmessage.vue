<template>
  <div>
    <div v-for="message in messages">
      <div class="d-flex justify-content-start mb-4" v-if="message.user == 'yoda'">
        <div class="img_cont_msg">
          <img src="~/assets/images/yoda.png" class="rounded-circle user_img_msg">
        </div>
        <div class="msg_container" v-html="message.message"></div>
      </div>
      <div class="d-flex justify-content-end mb-4" v-else-if="message.user == 'luke'">
        <div class="msg_container_send" v-text="message.message"></div>
        <div class="img_cont_msg">
          <img src="~/assets/images/luke.png" class="rounded-circle user_img_msg">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: [
    'messages'
  ]
}
</script>
