import Vue from 'vue'
import message from '../../../api/message'
import * as types from './mutation-types'

const state = {
	messages: [],
	lastMessages: false
}

// getters
const getters = {
	messages: state => state.messages
}

// actions
const actions = {
	getMessages ({ commit }, page) {
		return new Promise((resolve, reject) => {
			if (!state.lastMessages) {
				message.getMessages({
					page: page
				}, res => {
					commit(types.RECEIVE_MESSAGES, { res })
					resolve()
				}, err => {
					reject(err)
				})
			}
		})
	},
	addMessage ({ commit }, data) {
		commit(types.ADD_MESSAGE, { data })
	},
	modifyMessage ({ commit }, data) {
		commit(types.MODIFY_MESSAGE, { data })
	},
	resetMessages ({ commit }) {
		commit(types.RESET_MESSAGES)
	}
}

// mutations
const mutations = {
	[types.RECEIVE_MESSAGES] (state, { res }) {
		if (Object.entries(res).length) {
			if (Object.entries(state.messages).length) {
				state.messages = Object.assign({}, state.messages, res)
			} else {
				state.messages = res
			}
		} else {
			state.lastMessages = true
		}
	},
	[types.MODIFY_MESSAGE] (state, { data }) {
		data.properties.forEach(function(property, key) {
			Vue.set(state.messages[data.username], property.key, property.value)
		})
	},
	[types.ADD_MESSAGE] (state, { data }) {
		state.messages = Object.assign(data, state.messages)
	},
	[types.RESET_MESSAGES] (state) {
		state.messages = []
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}