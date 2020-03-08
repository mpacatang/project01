<template>
	<div v-if="notification" class="notification-box">
		<div class="d-lg-none position-fixed post-0 posl-0 w-100 z-index-1 font-weight-bolder p-15 text-center border-bottom border-color-1">Activity</div>
		<div class="notification">
			<div class="content">
				<template v-if="notifications.length">
					<template v-for="notification in notifications">
						<router-link v-if="notification.type == 'like'" tag="a" :to="{ name: 'single-post', params: { slug: notification.like_notification.like.post.slug } }" class="item px-15 py-10">
							<img class="mr-15 fw-40 fh-40 rounded-half-circle" :src="notification.like_notification.like.user.photo_url">
							<div class="item-content">
								<span class="user-name">{{ notification.like_notification.like.user.name }}</span> liked your photo. <span class="time">{{ timeAgo(notification.created_at) }}</span>
							</div>
							<div class="ml-15 position-relative fw-50 fh-40">
								<img class="position-absolute w-100 h-100 object-fit-cover rounded" :src="notification.like_notification.like.post.photo_url">
							</div>
						</router-link>
						<router-link v-if="notification.type == 'comment'" tag="a" :to="{ name: 'single-post', params: { slug: notification.comment_notification.comment.post.slug } }" class="item px-15 py-10">
							<img class="mr-15 fw-40 fh-40 rounded-half-circle" :src="notification.comment_notification.comment.user.photo_url">
							<div class="item-content">
								<span class="user-name">{{ notification.comment_notification.comment.user.name }}</span> commented: {{ notification.comment_notification.comment.comment }}. <span class="time">{{ timeAgo(notification.created_at) }}</span>
							</div>
							<div class="ml-15 position-relative fw-50 fh-40">
								<img class="position-absolute w-100 h-100 object-fit-cover rounded" :src="notification.comment_notification.comment.post.photo_url">
							</div>
						</router-link>
						<router-link v-if="notification.type == 'follow'" tag="a" :to="{ name: 'profile', params: { slug: notification.follow_notification.follow.follower_user.username } }" class="item px-15 py-10">
							<img class="mr-15 fw-40 fh-40 rounded-half-circle" :src="notification.follow_notification.follow.follower_user.photo_url">
							<div class="item-content">
								<span class="user-name">{{ notification.follow_notification.follow.follower_user.name }}</span> started following you. <span class="time">{{ timeAgo(notification.created_at) }}</span>
							</div>
						</router-link>
					</template>
				</template>
				<div v-else class="text-center p-10">
					You have no new notifications
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import user from '../../api/user'
	import helpers from '../../helpers'

    export default {
    	data() {
    		return {
    			page: 1,
    			notifications: [],
    			onLoadNotifications: false,
				lastNotifications: false
    		}
    	},
        props: ['notification'],
        watch: {
        	notification() {
        		if (!this.notifications.length) {
        			this.getNotifications()
        		}
			
				this.$nextTick(() => {
					this.initScrollPagination()
				})
        	}
        },
        methods: {
        	getNotifications() {
				let self = this
				self.onLoadNotifications = true
				if (!self.lastNotifications) {
					user.getNotifications({
						page: self.page
					}, res => {
						if (res.data.length) {
							if (self.notifications.length) {
								self.notifications = self.notifications.concat(res.data)
							} else {
								self.notifications = res.data
							}

							self.onLoadNotifications = false
						} else {
							self.lastNotifications = true
						}
					}, err => {
	    				console.log(err)
					})
				}
			},
        	initScrollPagination() {
				let self = this
				let notification = $('.notification')
				$(notification).scroll(function() {
					if (($(notification).scrollTop() + $(notification).height() > $(notification).find('.content').height() * 0.9) && !self.onLoadNotifications) {
						self.page++;
						self.getNotifications()
					}
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			}
        }
    }
</script>
