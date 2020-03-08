<template>
	<div class="stories-wrapper mx-auto bg-color-lg-9 mt-lg-20 border border-color-4 border-color-lg-1 rounded">
		<div class="d-none d-lg-flex w-100 justify-content-between px-15 pt-15">
			<div class="font-weight-bold text-color-7">Stories</div>
			<a @click="showStoryModal(0)" v-if="stories.length" class="font-weight-bolder text-size-13" href="javascript:;">Watch All</a>
		</div>
		<div class="stories-wrapper-box px-15 p-sm-0 px-lg-15 pb-lg-5 mt-lg-5">
			<div class="stories-box d-flex d-lg-block">
				<input type="file" class="d-none" ref="story-file" @change="showCreateStoryModal($event.target)">
				<div @click="choosePhoto" class="stories small-profile position-relative mr-15 mr-lg-0 d-flex flex-column flex-lg-row mb-lg-10 media align-items-center cursor-pointer">
					<img class="mr-lg-13 fw-66 fh-66 fw-lg-42 fh-lg-42 rounded-half-circle" :src="user.photo_url">
					<div class="media-body">
						<div class="user-name mx-auto mx-lg-0 mt-3 mt-lg-0 text-center text-lg-left font-weight-bolder text-size-13 text-size-lg-14">Your Story</div>
						<div class="font-weight-bold text-color-7 text-size-11 mt-1 d-none d-lg-block">CREATE YOUR STORY</div>
					</div>
					<i class="icon icon-plus text-size-17 text-size-lg-14 text-white position-absolute post-46 posl-49 post-lg-25 posl-lg-30 rounded-half-circle bg-primary"></i>
				</div>
				<div v-for="(story, key) in stories" @click="showStoryModal(key)" :class="getStoryClass(story)">
					<img class="mr-lg-13 fw-66 fh-66 fw-lg-42 fh-lg-42 rounded-half-circle" :src="story.following_user.photo_url">
					<div class="media-body">
						<div class="user-name mx-auto mx-lg-0 mt-3 mt-lg-0 text-center text-lg-left font-weight-bolder text-size-13 text-size-lg-14">{{ story.following_user.username }}</div>
						<div class="font-weight-bold text-color-7 text-size-11 mt-1 d-none d-lg-block text-uppercase">{{ timeAgo(story.following_user.stories.slice(-1)[0].created_at) }}</div>
					</div>
				</div>
			</div>
		</div>
		<story-modal :userKey="userKey" @changeUserKey="changeUserKey"></story-modal>
	</div>
</template>

<script>
	import StoryModal from './StoryModal'
	import story from '../../api/story'
	import helpers from '../../helpers'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		data() {
    		return {
    			page: 1,
    			userKey: null,
    			onLoadStories: false
    		}
    	},
    	computed: mapGetters({
    		user: 'main/user',
            stories: 'story/stories'
        }),
		methods: {
			getStories() {
				let self = this
				self.onLoadStories = true
				self.$store.dispatch('story/getStories', self.page).then(() => {
					self.onLoadStories = false
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			},
			initScrollPagination() {
				let self = this
				let storiesWrapperBox = $('.stories-wrapper-box')
				$(storiesWrapperBox).scroll(function() {
					if ($(window).width() >= 992) {
						if (($(storiesWrapperBox).scrollTop() + $(storiesWrapperBox).height() > $(storiesWrapperBox).find('.stories-box').height() * 0.9) && !self.onLoadStories) {
							self.page++;
							self.getStories()
						}
					} else {
						if (($(storiesWrapperBox).scrollLeft() + $(storiesWrapperBox).width() > $(storiesWrapperBox).find('.stories-box').width() * 0.9) && !self.onLoadStories) {
							self.page++;
							self.getStories()
						}
					}
				})
			},
			showStoryModal(userKey) {
				this.userKey = userKey
				$('#story-modal').modal('show')
			},
			changeUserKey(userKey) {
				this.userKey = userKey
			},
			choosePhoto() {
				$(this.$refs['story-file']).val('')
				$(this.$refs['story-file']).trigger('click')
			},
			showCreateStoryModal(input) {
				if (input.files && input.files[0]) {
					this.$store.dispatch('createStoryModal/setPhoto', input.files)
					$('#create-story-modal').modal('show')
                }
			},
			getStoryClass(story) {
				return {
					'with-stories': story.following_user.viewed_all_stories,
					'with-stories-active': !story.following_user.viewed_all_stories,
					'stories': true,
					'small-profile': true,
					'mr-15': true,
					'mr-lg-0': true,
					'd-flex': true,
					'flex-column': true,
					'flex-lg-row': true,
					'mb-lg-10': true,
					'media': true,
					'align-items-center': true,
					'cursor-pointer': true
				}
			}
		},
		mounted() {
			this.$store.dispatch('story/resetStories').then(() => {
				this.getStories()
				this.initScrollPagination()
			})
		},
		components: {
			'story-modal': StoryModal
		}
    }
</script>