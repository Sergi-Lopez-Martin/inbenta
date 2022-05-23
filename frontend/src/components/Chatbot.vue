<template>
    <div class="container-fluid h-100">
        <div class="row justify-content-center h-100">
            <div class="col-md-8 col-xl-6 chat">
                <div class="card">
                    <div class="card-header msg_head">
                        <div class="d-flex bd-highlight">
                            <div class="img_cont">
                                <img src="~/assets/images/yoda.png" class="rounded-circle user_img">
                                <span class="online_icon"></span>
                            </div>
                            <div class="user_info">
                                <span>Chat with Yoda</span>
                                <p>Online</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body msg_card_body">
                        <Chatmessage :messages="messages" />
                        <div class="d-flex justify-content-start mb-4" v-if="loading">
                            <div class="img_cont_msg">
                                <img src="~/assets/images/yoda.png" class="rounded-circle user_img_msg">
                            </div>
                            <div class="msg_container">
                                Yoda is writting...
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Chatinput v-on:newMessage="pushMessage" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chatmessage from './Chatmessage'
import Chatinput from './Chatinput'
import { mapGetters } from 'vuex'
export default {
    name: 'chatbot',
    components: { Chatmessage, Chatinput },
    computed: {
        ...mapGetters({
            conversation: 'getMessages',
            loading: 'getLoading'
        }),
        messages() {
            return this.conversation ? this.conversation : []
        }
    },
    methods: {
        pushMessage(message) {
            this.$store.dispatch('sendMessage', message)
        },
        scrollToBottom() {
            const container = this.$el.querySelector(".msg_card_body")
            container.scrollTop = container.scrollHeight
        }
    },
    mounted() {
        this.scrollToBottom()
    },
    watch: {
        messages: function () {
            this.$nextTick(() => {
                this.scrollToBottom()
            })
        }
    }
}
</script>
