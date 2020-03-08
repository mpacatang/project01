import Vue from 'vue'
import post from '../../../api/post'
import * as types from './mutation-types'

const state = {
	posts: [],
	lastPosts: false
}

// getters
const getters = {
	posts: state => state.posts
}

// actions
const actions = {
	getPosts ({ commit }, page) {
		return new Promise((resolve, reject) => {
			if (!state.lastPosts) {
				post.getPosts({
					page: page
				}, res => {
					commit(types.RECEIVE_POSTS, { res })
					resolve()
				}, err => {
					reject(err)
				})
			}
		})
	},
	addPost ({ commit }, data) {
		commit(types.ADD_POST, { data })
	},
	modifyPost ({ commit }, data) {
		commit(types.MODIFY_POST, { data })
	},
	resetPosts ({ commit }) {
		commit(types.RESET_POSTS)
	}
}

// mutations
const mutations = {
	[types.RECEIVE_POSTS] (state, { res }) {
		if (res.data.length) {
			res.data.map(function(post, key) {
				post.showAllComments = false
				post.newComments = []
				post.comment = ''
				post.deleted = false
				return post
			})

			if (state.posts.length) {
				state.posts = state.posts.concat(res.data)
			} else {
				state.posts = res.data
			}
		} else {
			state.lastPosts = true
		}
	},
	[types.MODIFY_POST] (state, { data }) {
		Vue.set(state.posts, data.key, data.post)
	},
	[types.ADD_POST] (state, { data }) {
		state.posts.unshift(data)
	},
	[types.RESET_POSTS] (state) {
		state.posts = []
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}