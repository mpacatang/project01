import post from '../../../api/post'
import * as types from './mutation-types'

const state = {
	postId: '',
	page: 1,
	likes: [],
	lastLikes: false,
    onLoadLikes: false
}

// getters
const getters = {
	page: state => state.page,
	likes: state => state.likes,
	onLoadLikes: state => state.onLoadLikes
}

// actions
const actions = {
	getLikes ({ commit }) {
		return new Promise((resolve, reject) => {
			commit(types.ON_LOAD_POSTS, true)
			if (!state.lastLikes) {
				post.getLikes({
					postId: state.postId,
					params: {
						page: state.page
					}
				}, res => {
					commit(types.RECEIVE_LIKES, { res })
					resolve()
				}, err => {
					reject(err)
				})
			}
		})
	},
	setPostId ({ commit }, postId) {
		commit(types.SET_POST_ID, postId)
	},
	setPage ({ commit }, page) {
		commit(types.SET_PAGE, page)
	}
}

// mutations
const mutations = {
	[types.RECEIVE_LIKES] (state, { res }) {
		if (res.data.length) {
			if (state.likes.length) {
				state.likes = state.likes.concat(res.data)
			} else {
				state.likes = res.data
			}

			state.onLoadLikes = false
		} else {
			state.lastLikes = true
		}
	},
	[types.ON_LOAD_POSTS] (state, val) {
		state.onLoadLikes = val
	},
	[types.SET_POST_ID] (state, postId) {
		state.page = 1
		state.likes = []
		state.postId = postId
		state.lastLikes = false
	},
	[types.SET_PAGE] (state, page) {
		state.page = page
	}
}

export default {
	namespaced: true,
    state,
    getters,
    actions,
    mutations
}