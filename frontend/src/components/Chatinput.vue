<template>
    <div class="input-group">
        <textarea v-model="messageText" @keyup.enter="sendMessage"
            class="form-control type_msg" placeholder="Type your message...">
        </textarea>
        <div class="input-group-append">
            <span class="input-group-text send_btn">
                <i @click="sendMessage" class="fas fa-location-arrow" />
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'chatinput',
    data() {
        return {
            messageText: null
        }
    },
    methods: {
        sendMessage() {
            if (this.messageText !== null) {
                this.$emit('newMessage', this.messageText)
                this.messageText = null
            }
        }
    }
}
</script>
