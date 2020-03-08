<template>
	<div :class="getSingleStoryModalClass()" :id="modalClass" :ref="modalClass" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div v-if="Object.keys(user).length" class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div v-if="stories.length" class="modal-body">
					<div class="media align-items-center">
						<img class="mr-13 fw-32 rounded-half-circle" :src="user.photo_url">
						<div class="media-body font-weight-bolder d-flex">
							<a @click="link({ name: 'profile', params: { username: user.username } })" class="text-white" href="javascript:;">{{ user.username }}</a>
							<div class="text-size-15 text-color-11 ml-8 mr-auto">{{ timeAgo(stories[activeStory].created_at) }}</div>
							<a class="d-flex align-items-center mr-5" href="">
								<i class="icon-options text-size-18 text-white"></i>
							</a>
						</div>
					</div>
					<div class="story-progress d-flex mt-13">
						<div v-for="(story, key) in stories" class="progress w-100 rounded-0 bg-color-10 fh-2">
							<div :class="getProgressBarClass(story.animate)" :style="getTimerStyle(story, key)" role="progressbar" :aria-valuenow="story.progressBarAnimation" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
					<div class="overflow-hidden bg-white rounded mt-8">
						<div class="close position-absolute mt-5 posr-0 mmr-18 d-none d-sm-block" data-dismiss="modal" aria-label="Close"></div>
						<div class="image-box w-100 position-relative mx-auto">
							<img class="position-absolute pos-0 w-100 h-100 object-fit-cover rounded" :src="stories[activeStory].photo_url">
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
    	props: ['user', 'modalClass'],
    	watch: {
    		user() {
    			this.stories = this.user.stories
    		}
    	},
		methods: {
			storyTimer() {
				let self = this
				self.setActiveStory()
				self.setProgressBar()

				setTimeout(() => {
					self.startProgressBar()
				}, 500)

				self.storyTimerInterval = setInterval(() => {
					if (self.activeStory + 1 == self.stories.length) {
						$('#' + this.modalClass).modal('hide')
						clearInterval(self.storyTimerInterval)
					} else {
						self.activeStory += 1
						self.startProgressBar()
					}
				}, 5000)
			},
			setProgressBar() {
				let self = this
				let stories = Object.assign([], this.stories)

				stories.map(function(story, key) {
					story.animate = false
					if (self.user.viewed_all_stories) {
						story.progressBarAnimation = 0
					} else {
						if (self.user.last_viewed_story_index == null) {
							story.progressBarAnimation = 0
						} else {
							story.progressBarAnimation = key < self.user.last_viewed_story_index + 1 ? 100 : 0
						}
					}
					return story
				})

				this.$emit('setProfile', {
					key: 'stories',
					value: stories
				})
			},
			setActiveStory() {
				if (this.user.viewed_all_stories) {
					this.activeStory = 0
				} else {
					this.activeStory = this.user.last_viewed_story_index == null ? 0 : this.user.last_viewed_story_index + 1;
				}
			},
			startProgressBar() {
				this.stories[this.activeStory].animate = true
				let stories = Object.assign([], this.stories)
				stories[this.activeStory].progressBarAnimation = 100

				this.$emit('setProfile', {
					key: 'stories',
					value: stories
				})

				this.$emit('setProfile', {
					key: 'last_viewed_story_index',
					value: this.activeStory
				})

				if (this.user.viewed_all_stories) {
					this.$emit('setProfile', {
						key: 'viewed_all_stories',
						value: true
					})
				} else {
					this.$emit('setProfile', {
						key: 'viewed_all_stories',
						value: this.user.stories.length == this.activeStory + 1
					})
				}

				this.viewStory(this.user.stories[this.activeStory].id)
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
				$('#' + this.modalClass).modal('hide')
				this.$router.push(route)
			},
			initSingleStoryModal() {
				let self = this
				$('#' + this.modalClass).on('show.bs.modal', function() {
					self.storyTimer()
				})

				$('#' + this.modalClass).on('hide.bs.modal', function() {
					clearInterval(self.storyTimerInterval)
				})
			},
			getSingleStoryModalClass() {
				let singleStoryModalClass = {}
				singleStoryModalClass[this.modalClass] = true
				singleStoryModalClass['story-modal'] = true
				singleStoryModalClass['modal'] = true
				singleStoryModalClass['fade'] = true
				return singleStoryModalClass
			}
		},
		mounted() {
			this.initSingleStoryModal()

			if (Object.keys(this.user).length) {
				this.stories = this.user.stories
			}
		}
    }
</script>