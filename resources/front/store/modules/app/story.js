import Vue from 'vue'
import story from '../../../api/story'
import * as types from './mutation-types'

const state = {
	stories: [],
	lastStories: false
}

// getters
const getters = {
	stories: state => state.stories
}

// actions
const actions = {
	getStories ({ commit }, page) {
		return new Promise((resolve, reject) => {
			if (!state.lastStories) {
				story.getStories({
					page: page
				}, res => {
					commit(types.RECEIVE_STORIES, { res })
					resolve()
				}, err => {
					reject(err)
				})
			}
		})
	},
	setUserStory ({ commit }, data) {
		commit(types.SET_STORY, { data })
	},
	resetStories ({ commit }) {
		return new Promise((resolve, reject) => {
			commit(types.RESET_STORIES)
			resolve()
		})
	}
}

// mutations
const mutations = {
	[types.RECEIVE_STORIES] (state, { res }) {
		if (res.data.length) {
			res.data = res.data.map(function(users, key) {
				users.following_user.stories = users.following_user.stories.map(function(story, key) {
					story.animate = false
					story.progressBarAnimation = 0
					return story
				})

				return users
			})

			if (state.stories.length) {
				state.stories = state.stories.concat(res.data)
			} else {
				state.stories = res.data
			}
		} else {
			state.lastStories = true
		}
	},
	[types.SET_STORY] (state, { data }) {
		Vue.set(state.stories, data.key, data.userStory)
	},
	[types.RESET_STORIES] (state) {
		state.stories = []
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}