import * as types from './mutation-types'

const state = {
	activeMobile: false
}

// getters
const getters = {
	activeMobile: state => state.activeMobile,
}

// actions
const actions = {
	activateMobile ({ commit }, data) {
		commit(types.SET_ACTIVE_MOBILE, { data })
	}
}

// mutations
const mutations = {
	[types.SET_ACTIVE_MOBILE] (state, { data }) {
		state.activeMobile = data
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}