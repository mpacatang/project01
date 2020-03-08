<template>
	<div class="story-modal modal fade" id="story-modal" ref="story-modal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div v-if="stories.length && userKey !== null" class="modal-body">
					<div class="media align-items-center">
						<img class="mr-13 fw-32 rounded-half-circle" :src="stories[userKey].following_user.photo_url">
						<div class="media-body font-weight-bolder d-flex">
							<a @click="link({ name: 'profile', params: { username: stories[userKey].following_user.username } })" class="text-white" href="javascript:;">{{ stories[userKey].following_user.username }}</a>
							<div class="text-size-15 text-color-11 ml-8 mr-auto">{{ timeAgo(stories[userKey].following_user.stories[activeStory].created_at) }}</div>
							<a class="d-flex align-items-center mr-5" href="">
								<i class="icon-options text-size-18 text-white"></i>
							</a>
						</div>
					</div>
					<div class="story-progress d-flex mt-13">
						<div v-for="(story, key) in stories[userKey].following_user.stories" class="progress w-100 rounded-0 bg-color-10 fh-2">
							<div :class="getProgressBarClass(story.animate)" :style="getTimerStyle(story, key)" role="progressbar" :aria-valuenow="story.progressBarAnimation" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
					<div class="overflow-hidden bg-white rounded mt-8">
						<div class="close position-absolute mt-5 posr-0 mmr-18 d-none d-sm-block" data-dismiss="modal" aria-label="Close"></div>
						<div class="image-box w-100 position-relative mx-auto">
							<img class="position-absolute pos-0 w-100 h-100 object-fit-cover rounded" :src="stories[userKey].following_user.stories[activeStory].photo_url">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import story from '../../api/story'
	import helpers from '../../helpers'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		data() {
    		return {
    			stories: [],
    			activeStory: 0,
    			storyTimerInterval: ''
    		}
    	},
    	computed: {
    		...mapGetters({
	            userStories: 'story/stories'
	        })
    	},
    	watch: {
    		userStories() {
    			this.stories = this.userStories
    		},
    		userKey() {
    			if (this.userKey !== null) {
    				this.storyTimer()
    			}
    		}
    	},
		props: ['userKey'],
		methods: {
			storyTimer() {
				let self = this
				self.setActiveStory()
				self.setProgressBar()

				setTimeout(() => {
					self.startProgressBar()
				}, 500)

				self.storyTimerInterval = setInterval(() => {
					if (self.activeStory + 1 == self.stories[self.userKey].following_user.stories.length) {
						if (self.userKey + 1 < self.stories.length) {
							self.resetCurrentStoryProperties()
							self.$emit('changeUserKey', self.userKey + 1)
						} else {
							$('#story-modal').modal('hide')
							clearInterval(self.storyTimerInterval)
						}
					} else {
						self.activeStory += 1
						self.startProgressBar()
					}
				}, 5000)
			},
			setProgressBar() {
				let userStory = Object.assign({}, this.stories[this.userKey])
				userStory.following_user.stories.map(function(story, key) {
					story.animate = false
					if (userStory.following_user.viewed_all_stories) {
						story.progressBarAnimation = 0
					} else {
						if (userStory.following_user.last_viewed_story_index == null) {
							story.progressBarAnimation = 0
						} else {
							story.progressBarAnimation = key < userStory.following_user.last_viewed_story_index + 1 ? 100 : 0
						}
					}
					return story
				})
				this.$store.dispatch('story/setUserStory', {
					key: this.userKey,
					userStory: userStory
				})
			},
			setActiveStory() {
				let followingUser = this.stories[this.userKey].following_user
				if (followingUser.viewed_all_stories) {
					this.activeStory = 0
				} else {
					this.activeStory = followingUser.last_viewed_story_index == null ? 0 : followingUser.last_viewed_story_index + 1;
				}
			},
			startProgressBar() {
				this.stories[this.userKey].following_user.stories[this.activeStory].animate = true
				let userStory = Object.assign({}, this.stories[this.userKey])
				userStory.following_user.stories[this.activeStory].progressBarAnimation = 100
				userStory.following_user.last_viewed_story_index = this.activeStory

				if (userStory.following_user.viewed_all_stories) {
					userStory.following_user.viewed_all_stories = true
				} else {
					userStory.following_user.viewed_all_stories = userStory.following_user.stories.length == this.activeStory + 1
				}

				this.$store.dispatch('story/setUserStory', {
					key: this.userKey,
					userStory: userStory
				})

				this.viewStory(userStory.following_user.stories[this.activeStory].id)
			},
			resetCurrentStoryProperties() {
				this.$emit('changeUserKey', null)
				clearInterval(this.storyTimerInterval)
			},
			getTimerStyle(story, key) {
				return {
					'width': story.progressBarAnimation + '%'
				}
			},
			getProgressBarClass(animate) {
				return {
					'bg-white': true,
					'progress-bar': true,
					'animate': animate
				}	
			},
			viewStory(storyId) {
				story.viewStory({
					storyId: storyId
				}, res => {
					
				}, err => {
    				console.log(err)
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			},
			link(route) {
				$('#story-modal').modal('hide')
				this.$router.push(route)
			},
			initStoryModal() {
				let self = this
				$('#story-modal').on('hide.bs.modal', function() {
					self.resetCurrentStoryProperties()
				})
			}
		},
		mounted() {
			this.initStoryModal()
		}
    }
</script>