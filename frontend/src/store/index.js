export const state = () => ({
    conversation: [],
    sessionId: null,
    loading: false
})

export const mutations = {
    sendMessage(state, message) {
        state.conversation.push(message)
    },
    setSessionId(state, sessionId) {
        state.sessionId = sessionId
    },
    setLoading(state, loading) {
        state.loading = loading
    }
}

export const actions = {
    async sendMessage({commit, state}, message, endpoint=`/message`) {
        commit('sendMessage', {'user':'luke', 'message':message})
        commit('setLoading', true)
        let header = {
            'api-key': this.$config.API_KEY,
            'sessionID': state.sessionId
        }
        try {
            const data = await fetch(this.$config.API_URL+endpoint, {
                method: 'POST',
                headers: header,
                body: message
            })
            if(data.status === 200) {
                data.text().then(res => {
                    let response = JSON.parse(res)
                    commit('setLoading', false)
                    commit('sendMessage', response.answer)
                    commit('setSessionId', response.sessionID)
                })
            } else {
                commit('setLoading', false)
                alert("Connection error."+data.statusText)
            }
        } catch(e) {
            commit('setLoading', false)
            alert("Connection error."+e.error)
        }
    },
}

export const getters = {
    getMessages: (state) => {
        return state.conversation
    },
    getSessionId: (state) => {
        return state.sessionId
    },
    getLoading: (state) => {
        return state.loading
    },
}