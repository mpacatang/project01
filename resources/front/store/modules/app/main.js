import user from '../../../api/user'
import * as types from './mutation-types'

const state = {
	user: [],
	report: {
		text: '',
		show: false,
		timer: ''
	}
}

// getters
const getters = {
	user: state => state.user,
	report: state => state.report
}

// actions
const actions = {
	getProfile ({ commit }) {
		return new Promise((resolve, reject) => {
			user.getProfile(user => {
				commit(types.RECEIVE_USER, { user })
				resolve()
			}, err => {
				reject(err)
			})
		})
	},
	setReport ({ commit }, report) {
		commit(types.SET_REPORT, { report })
	}
}

// mutations
const mutations = {
	[types.RECEIVE_USER] (state, { user }) {
		user.stories = user.stories.map(function(story, key) {
			story.animate = false
			story.progressBarAnimation = 0
			return story
		})

		state.user = user
	},
	[types.SET_REPORT] (state, { report }) {
		state.report.show = true
		state.report.text = report
		clearTimeout(state.report.timer)
		state.report.timer = setTimeout(() => {
			state.report.show = false
		}, 3000)
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}