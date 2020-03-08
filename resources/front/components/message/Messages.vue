<template>
	<div class="container mt-100">
		<div class="message-wrapper bg-white border border-color-1 d-flex rounded mb-60">
			<div class="message-list fw-75 fw-lg-auto flex-fill h-100 d-flex flex-column">
				<div class="px-15 border-bottom border-color-1">
					<div class="fh-55 fh-lg-45 d-flex align-items-center justify-content-center justify-content-lg-between">
						<div class="font-weight-bolder d-none d-lg-block">Messages</div>
						<a href=""><i class="icon icon-equalizer" aria-hidden="true"></i></a>
					</div>
				</div>
				<div class="message-wrapper-box overflow-y-auto overflow-x-hidden">
					<div class="message-wrapper-list">
						<template v-for="message in messages">
							<router-link tag="a" :to="{ name: 'message-details', params: { username: message.user.username } }" class="message media cursor-pointer align-items-center fh-75 px-10 px-lg-15 py-8" active-class="active" exact>
								<div class="mr-lg-13 fw-48 position-relative">
									<img class="w-100 h-100 rounded-half-circle" :src="message.user.photo_url">
									<div v-if="message.unread" class="position-absolute post-0 posl-0 bg-danger px-5 pt-3 pb-5 text-white text-size-10 lh-9 font-weight-bolder rounded-half-circle">{{ message.unread }}</div>
								</div>
								<div class="media-body d-none d-lg-block">
									<div class="user-name font-weight-bolder">{{ message.user.username }}</div>
									<div class="text">{{ message.last_message_details.message }}</div>
									<div class="text-size-12 text-color-5">{{ timeAgo(message.last_message_details.created_at) }}</div>
								</div>
							</router-link>
						</template>
					</div>
				</div>
			</div>
			<router-view></router-view>
		</div>
	</div>
</template>

<script>
	import message from '../../api/message'
	import helpers from '../../helpers'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			page: 1,
    			onLoadMessages: false
    		}
    	},
    	computed: {
    		...mapGetters({
	            messages: 'message/messages'
        	})
        },
        methods: {
        	getMessages() {
				let self = this
				self.onLoadMessages = true
				self.$store.dispatch('message/getMessages', self.page).then(() => {
					self.onLoadMessages = false
				})
			},
			initScrollPagination() {
				let self = this
				$('.message-wrapper-box').scroll(function() {
					if (($('.message-wrapper-box').scrollTop() + $('.message-wrapper-box').height() > $('.message-wrapper-box').find('.message-wrapper-list').height() * 0.9) && !self.onLoadMessages) {
						self.page++;
						self.getMessages()
					}
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			}
        },
        mounted() {
        	this.getMessages()
			this.initScrollPagination()
        }
    }
</script>