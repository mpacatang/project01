import Vue from 'vue'
import Vuex from 'vuex'
import main from './modules/app/main'
import feed from './modules/app/feed'
import story from './modules/app/story'
import message from './modules/app/message'
import searchBox from './modules/app/search-box'
import likesModal from './modules/app/likes-modal'
import messageDetail from './modules/app/message-detail'
import createStoryModal from './modules/app/create-story-modal'

Vue.use(Vuex)

export default new Vuex.Store({
	modules: {
		main,
		feed,
		story,
		message,
		searchBox,
		likesModal,
		messageDetail,
		createStoryModal
	}
})