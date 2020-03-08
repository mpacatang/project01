<template>
	<section>
		<div class="top-bar-box">
			<div class="full-width-container container">
				<div class="top-bar">
					<router-link tag="a" :to="{ name: 'feed' }" class="logo-box">
						<i class="logo icon-camera" aria-hidden="true"></i>
						<div class="title">Yuup</div>
					</router-link>
					<search-box></search-box>
					<div class="top-side-box">
						<router-link tag="div" :to="{ name: 'explore' }" class="item-box">
							<i class="icon icon-compass" aria-hidden="true"></i>
						</router-link>
						<router-link tag="div" :to="{ name: 'messages' }" class="item-box">
							<i class="icon icon-bubbles" aria-hidden="true"></i>
						</router-link>
						<div class="mobile-nav-section">
							<router-link tag="div" :to="{ name: 'feed' }" class="item-box d-lg-none">
								<i class="icon icon-grid" aria-hidden="true"></i>
							</router-link>
							<div @click="activateMobile" class="item-box d-lg-none search-box-toggle">
								<i class="icon icon-magnifier" aria-hidden="true"></i>
							</div>
							<div class="item-box d-lg-none" data-toggle="modal" data-target="#create-post-modal">
								<i class="icon icon-plus" aria-hidden="true"></i>
							</div>
							<router-link tag="div" :to="{ name: 'suggestions' }" class="item-box">
								<i class="icon icon-user-follow" aria-hidden="true"></i>
							</router-link>
							<div @click="showNotification" class="item-box notification-toggle">
								<i class="icon icon-heart" aria-hidden="true"></i>
								<notification :notification="notification"></notification>
							</div>
							<router-link v-if="Object.keys(user).length" tag="div" :to="{ name: 'profile', params: { username: user.username } }" class="item-box">
								<i class="icon icon-user" aria-hidden="true"></i>
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</div>
		<report></report>
		<likes-modal></likes-modal>
		<router-view></router-view>
	</section>
</template>

<script>
	import moment from 'moment'
	import Report from './Report'
	import SearchBox from './SearchBox'
	import realtime from '../../api/realtime'
	import Notification from './Notification'
	import LikesModal from '../likes-modal/LikesModal'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
			return {
				notification: false
			}
		},
    	computed: mapGetters({
    		user: 'main/user',
    		messages: 'message/messages',
    		messageDetails: 'messageDetail/messages',
            activeMobile: 'searchBox/activeMobile'
        }),
        methods: {
        	setToken() {
                let token = JSON.parse(localStorage.getItem('token'))
                window.axios.defaults.headers.common['Authorization'] = token.token_type + ' ' + token.access_token
            },
            initTopBar() {
                if ($(window).scrollTop() > 40) {
					$('.top-bar-box').addClass('scrolled')
				}

				$(window).scroll(function() {
					if ($(window).scrollTop() > 40) {
						$('.top-bar-box').addClass('scrolled')
					} else {
						$('.top-bar-box').removeClass('scrolled')
					}
				})
            },
            getProfile() {
                this.$store.dispatch('main/getProfile')
            },
            activateMobile() {
        		this.$store.dispatch('searchBox/activateMobile', this.activeMobile ? false : true)
        	},
        	initSearchBoxMobileToggler() {
        		let self = this
        		$(document).on('click', function(event) {
        			if (!$(event.target).closest('.search-box-toggle').length && !$(event.target).closest('.search-box').length) {
						self.$store.dispatch('searchBox/activateMobile', false)
					}
        		})
        	},
        	initNotificationToggler() {
        		let self = this
        		$(document).on('click', function(event) {
        			if (!$(event.target).closest('.notification-toggle').length) {
						self.notification = false
					}
        		})
        	},
			onTabInactive() {
				window.onblur = () => {
					$('#story-modal').modal('hide')
					$('#single-story-modal').modal('hide')
				}
			},
			initModal() {
				$(document).on('show.bs.modal', '.modal', function() {
					var zIndex = 1040 + (10 * $('.modal:visible').length)
					$(this).css('z-index', zIndex)
					setTimeout(function() {
						$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack')
					}, 0)
				})
			},
			showNotification() {
				this.notification = this.notification ? false : true
			},
			expiredSession() {
				let token = JSON.parse(localStorage.getItem('token'))
				if (token.login_time !== false) {
					let currentTime = moment(new Date())
					let duration = moment.duration(currentTime.diff(moment(token.login_time)))

					if (duration.asSeconds() >= 1440) {
						localStorage.removeItem('token')
		    			this.$store.dispatch('feed/resetPosts')
		    			this.$store.dispatch('story/resetStories')
		    			this.$router.push({
							name: 'login'
						})
					}
				}
			},
			initRealtime() {
				let self = this, isTimeout = false, reqParams = {}

				if (Object.keys(self.messages).length && (self.$route.name == 'messages' || self.$route.name == 'message-details')) {
					reqParams['messages'] = Object.keys(self.messages).map(function(message, key) {
						return {
							username: self.messages[message].user.username,
							last_message_details_id: self.messages[message].last_message_details.id
						}
					})

					reqParams['new_messages'] = {
						last_message_id: self.messages[Object.keys(self.messages)[0]].id
					}
				}

				if (self.messageDetails.length && self.$route.name == 'message-details') {
					reqParams['message_details'] = {
						username: self.$route.params.username,
						last_message_details_id: self.messageDetails[self.messageDetails.length - 1].id
					}
				}

				realtime.getRealtime(reqParams, res => {
					if (res.messages) {
						res.messages.map(function(message, key) {
							self.$store.dispatch('message/modifyMessage', {
								username: message.user.username,
								properties: [{
									key: 'unread',
									value: message.unread
								}, {
									key: 'last_message_details',
									value: message.last_message_details
								}]
							})
						})
					}

					if (res.message_details) {
						if (self.$route.name == 'message-details' && self.$route.params.username == res.message_details.user.username) {
							if (res.message_details.messages.length) {
								self.$store.dispatch('messageDetail/triggerNewMessages', true)
								self.$store.dispatch('messageDetail/addMessages', res.message_details.messages)
							}

							let unReadMessage = self.messageDetails.filter(function(message, key) {
								return message.read == 0
							})

							if (unReadMessage.length) {
								if (res.message_details.last_read_message_id > unReadMessage[unReadMessage.length - 1].id) {
									self.messageDetails.map(function(message, key) {
										if (message.id < res.message_details.last_read_message_id) {
											message.read = 1
											self.$store.dispatch('messageDetail/modifyMessage', {
												key: key,
												message: message
											})
										}
									})
								}
							}
						}
					}

					if (res.new_messages) {
						if (Object.keys(res.new_messages).length) {
							self.$store.dispatch('message/addMessage', res.new_messages)
						}
					}

					if (isTimeout) {
						self.initRealtime()
					}
				}, err => {
    				console.log(err)
				})

				setTimeout(() => {
					isTimeout = true
					self.initRealtime()
				}, 10000)
			}
        },
        components: {
        	'report': Report,
			'search-box': SearchBox,
			'likes-modal': LikesModal,
			'notification': Notification
		},
        created() {
            this.setToken()
        },
        mounted() {
        	this.initModal()
            this.initTopBar()
            this.getProfile()
        	this.initRealtime()
            this.onTabInactive()
            this.expiredSession()
            this.initSearchBoxMobileToggler()
            this.initNotificationToggler()
        }
    }
</script>
