<template>
	<div class="w-100 w-lg-77 h-100 d-flex flex-column">
		<message-details-header></message-details-header>
		<div class="message-box h-100 overflow-y-auto overflow-x-hidden">
			<div class="d-flex flex-column p-20">
				<template v-for="message in messageDetails">
					<div v-if="message.user.id != user.id" :class="getMessageClass('left')">
						<img class="mr-20 fw-40 fh-40 rounded-half-circle" :src="message.user.photo_url">
						<div class="media-body">
							<template v-for="message in message.messages">
								<div :ref="'message-' + message.id" class="mb-10 float-left">
									<div class="content px-10 py-8 border border-color-1 rounded lh-22">{{ message.content }}</div>
									<div class="mt-7 text-size-11 text-color-5">{{ timeAgo(message.time) }}</div>
								</div>
								<div class="clearfix"></div>
							</template>
						</div>
					</div>
					<div v-else :class="getMessageClass('right')">
						<div class="media-body">
							<template v-for="message in message.messages">
								<div :ref="'message-' + message.id" class="mb-10 float-right text-right">
									<div class="content d-inline-block px-10 py-8 border border-color-1 rounded lh-22">{{ message.content }}</div>
									<div class="text-right mt-7 text-size-11 text-color-5">
										<template v-if="message.read">
											<span>Read</span>
											<div class="fw-3 fh-3 bg-color-2 rounded-circle d-none d-lg-inline-block mx-3"></div>
										</template>
										<span>{{ timeAgo(message.time) }}</span>
									</div>
								</div>
								<div class="clearfix"></div>
							</template>
						</div>
						<img class="ml-20 fw-40 fh-40 rounded-half-circle" :src="message.user.photo_url">
					</div>
				</template>
			</div>
		</div>
		<div class="border-top border-color-1">
			<form @keyup.enter="postMessage" class="fh-44 d-flex align-items-center">
				<textarea v-model="message" class="form-control h-100 pt-12 pr-40 resize-none rounded-0 border-0" placeholder="Say something..."></textarea>
				<button @click="postMessage" class="btn bg-white pl-5 mr-7 fw-40 fh-40"><i class="icon icon-paper-plane text-size-18 text-color-5" aria-hidden="true"></i></button>
			</form>
		</div>
	</div>
</template>

<script>
	import helpers from '../../helpers'
	import message from '../../api/message'
	import { mapGetters, mapActions } from 'vuex'
	import MessageDetailsHeader from './MessageDetailsHeader'

    export default {
    	data() {
    		return {
    			page: 1,
    			message: '',
    			scrollState: '',
    			firstRender: true,
    			onLoadMessages: false
    		}
    	},
    	computed: {
    		...mapGetters({
	    		user: 'main/user',
	            messages: 'messageDetail/messages',
	            onNewMessages: 'messageDetail/onNewMessages'
	        }),
    		messageDetails() {
    			return this.formatMessage(this.messages)
    		}
    	},
    	watch: {
    		'$route'(to, from) {
    			this.page = 1
        		this.firstRender = true
        		this.$store.dispatch('messageDetail/resetMessages')
        		this.getMessages()
	        	this.readMessages()
			},
			messages() {
				this.initRenderMessages()

				if (this.onNewMessages) {
					this.readMessages()
					this.scrollToBottom()
					this.$store.dispatch('messageDetail/triggerNewMessages', false)
				}
			}
    	},
        methods: {
    		getCurrentScrollState() {
    			if (this.messages.length) {
    				return {
    					lastMessageId: this.messages[0].id,
    					scrollOffset: $(this.$refs['message-' + this.messages[0].id]).offset().top - $('.message-box').scrollTop()
    				}
    			}
    		},
        	getMessages() {
				let self = this
				self.onLoadMessages = true
				self.scrollState = this.getCurrentScrollState()
				self.$store.dispatch('messageDetail/getMessages', {
					username: self.$route.params.username,
					params: {
						page: self.page
					}
				}).then(() => {
					self.onLoadMessages = false
					
					if (self.page > 1) {
						self.persistScrollState()
					} else {
						this.scrollToBottom()
					}
				})
			},
			persistScrollState() {
				if (this.scrollState !== undefined) {
					this.$nextTick(() => {
						$('.message-box').scrollTop($(this.$refs['message-' + this.scrollState.lastMessageId]).offset().top - this.scrollState.scrollOffset);
					})
				}
			},
			initScrollPagination() {
				let self = this
				$('.message-box').scroll(function() {
					if ($('.message-box').scrollTop() < 20 && !self.onLoadMessages) {
						self.page++
						self.getMessages()
					}
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			},
			postMessage() {
				message.postMessage({
					username: this.$route.params.username,
					params: {
						message: this.message
					}
				}, res => {
					this.message = ''

					if (!this.messageDetails.length) {
						this.scrollToBottom()
						this.$store.dispatch('messageDetail/addMessages', [res])
					}
				}, err => {
    				console.log(err)
				})
			},
			scrollToBottom() {
				this.$nextTick(() => {
					$('.message-box').scrollTop($('.message-box').children().height())
				})
			},
			formatMessage(messages) {
				let formattedMessages = [], lastSenderId = null
				messages.map(function(message, key) {
					if (lastSenderId == null || lastSenderId != message.sender_id) {
						formattedMessages.push({
							user: message.user,
							messages: [{
								id: message.id,
								read: message.read,
								content: message.message,
								time: message.created_at
							}]
						})
					} else {
						formattedMessages[formattedMessages.length - 1].messages.push({
							id: message.id,
							read: message.read,
							content: message.message,
							time: message.created_at
						})
					}

					lastSenderId = message.sender_id
					return message
				})

				return formattedMessages
			},
			getMessageClass(messagePosition) {
				if (messagePosition == 'left') {
					if (this.firstRender) {
						return {
							'media': true,
							'w-80': true,
							'w-md-60': true,
							'fadeInLeft': true,
							'animated': true
						}
					} else {
						return {
							'media': true,
							'w-80': true,
							'w-md-60': true
						}
					}
				} else {
					if (this.firstRender) {
						return {
							'media': true,
							'w-80': true,
							'w-md-60': true,
							'ml-auto': true,
							'fadeInRight': true,
							'animated': true
						}
					} else {
						return {
							'media': true,
							'w-80': true,
							'w-md-60': true,
							'ml-auto': true
						}
					}
				}
			},
			initRenderMessages() {
				if (Object.keys(this.messages).length) {
					let self = this
					setTimeout(() => {
						self.firstRender = false
					}, 1000)
				}
			},
			readMessages() {
				message.readMessages({
					username: this.$route.params.username
				}, res => {
					
				}, err => {
    				console.log(err)
				})
			}
        },
        components: {
			'message-details-header': MessageDetailsHeader
		},
        mounted() {
        	this.$store.dispatch('messageDetail/resetMessages')
        	this.getMessages()
        	this.readMessages()
			this.initScrollPagination()
        }
    }
</script>