import Vue from 'vue'
import * as types from './mutation-types'

const state = {
	photo: ''
}

// getters
const getters = {
	photo: state => state.photo
}

// actions
const actions = {
	setPhoto ({ commit }, data) {
		commit(types.SET_PHOTO, { data })
	}
}

// mutations
const mutations = {
	[types.SET_PHOTO] (state, { data }) {
		state.photo = data
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}