import Vue from 'vue'
import message from '../../../api/message'
import * as types from './mutation-types'

const state = {
	messages: [],
	lastMessages: false,
	onNewMessages: false
}

// getters
const getters = {
	messages: state => state.messages,
	onNewMessages: state => state.onNewMessages
}

// actions
const actions = {
	getMessages ({ commit }, data) {
		return new Promise((resolve, reject) => {
			if (!state.lastMessages) {
				message.getMessageDetails(data, res => {
					commit(types.RECEIVE_MESSAGES, { res })
					resolve()
				}, err => {
					reject(err)
				})
			}
		})
	},
	addMessages ({ commit }, data) {
		commit(types.ADD_MESSAGE, { data })
	},
	modifyMessage ({ commit }, data) {
		commit(types.MODIFY_MESSAGE, { data })
	},
	resetMessages ({ commit }) {
		commit(types.RESET_MESSAGES)
	},
	triggerNewMessages ({ commit }, data) {
		commit(types.ON_NEW_MESSAGES, { data })
	}
}

// mutations
const mutations = {
	[types.RECEIVE_MESSAGES] (state, { res }) {
		if (res.length) {
			if (state.messages.length) {
				state.messages = [...res, ...state.messages]
			} else {
				state.messages = res
			}
		} else {
			state.lastMessages = true
		}
	},
	[types.MODIFY_MESSAGE] (state, { data }) {
		Vue.set(state.messages, data.key, data.message)
	},
	[types.ADD_MESSAGE] (state, { data }) {
		state.messages = [...state.messages, ...data]
	},
	[types.RESET_MESSAGES] (state) {
		state.messages = []
		state.lastMessages = false
	},
	[types.ON_NEW_MESSAGES] (state, { data }) {
		state.onNewMessages = data
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}