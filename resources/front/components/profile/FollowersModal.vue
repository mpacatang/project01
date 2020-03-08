<template>
	<div class="follow-modal modal fade" id="followers-modal" ref="followers-modal" tabindex="-1" role="dialog" aria-labelledby="likesModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<div class="w-100 position-relative px-15 py-12 border-bottom border-color-1">
							<div class="font-weight-bolder text-center">Followers</div>
							<div class="close position-absolute posr-15 my-auto posy-0" data-dismiss="modal" aria-label="Close"></div>
						</div>
						<div class="follow-box py-7">
							<div class="follow">
								<div v-for="(follower, key) in followers" class="media align-items-center px-16 py-8">
									<img class="fw-44 fh-44 mr-12 rounded-half-circle" :src="follower.follower_user.photo_url">
									<div class="media-body media-body d-flex align-items-center">
										<div class="mr-auto">
											<router-link tag="a" :to="{ name: 'profile', params: { username: follower.follower_user.username } }" class="username font-weight-bolder text-size-14">{{ follower.follower_user.username }}</router-link>
											<div class="name font-weight-bold text-color-7">{{ follower.follower_user.name }}</div>
										</div>
										<button @click="follow(key, follower.follower_user.id)" v-if="!follower.follower_user.following" class="btn btn-primary font-weight-bolder fh-30 py-0">Follow</button>
										<button @click="follow(key, follower.follower_user.id)" v-else class="btn btn-outline-secondary font-weight-bolder border-color-2 text-color-12 fh-30 py-0">Following</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import user from '../../api/user'
    import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			page: 1,
    			followers: [],
    			onLoadFollowers: false,
				lastFollowers: false
    		}
    	},
    	props: ['user'],
	    methods: {
			getFollowers() {
				let self = this
				self.onLoadFollowers = true
				if (!self.lastFollowers) {
					user.getFollowers({
						userId: this.user.id,
						params: {
							page: self.page
						}
					}, res => {
						if (res.data.length) {
							if (self.followers.length) {
								self.followers = self.followers.concat(res.data)
							} else {
								self.followers = res.data
							}

							self.onLoadFollowers = false
						} else {
							self.lastFollowers = true
						}
					}, err => {
	    				console.log(err)
					})
				}
			},
			follow(key, userId) {
				user.follow({
					userId: userId
				}, res => {
					this.followers[key].follower_user.following = this.followers[key].follower_user.following ? 0 : 1

					if (!this.followers[key].follower_user.following) {
						this.$store.dispatch('main/setReport', 'You have stopped following @' + this.followers[key].follower_user.username + '.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have started following @' + this.followers[key].follower_user.username + '.')	
					}
				}, err => {
    				console.log(err)
				})	
			},
			initScrollPagination() {
				let self = this
				let followBox = $(self.$refs['followers-modal']).find('.follow-box')
				$(followBox).scroll(function() {
					if (($(followBox).scrollTop() + $(followBox).height() > $(followBox).find('.follow').height() * 0.9) && !self.onLoadFollowings) {
						self.page++;
						self.getFollowers()
					}
				})
			},
			initModal() {
				let self = this
				$('#followers-modal').on('show.bs.modal', function() {
					self.page = 1
	    			self.followers = []
	    			self.onLoadFollowers = false
					self.lastFollowers = false
					self.getFollowers()
    				self.initScrollPagination()
				})
			}
	    },
	    mounted() {
	    	this.initModal()
	    }
    }
</script>