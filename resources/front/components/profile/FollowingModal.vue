<template>
	<div class="follow-modal modal fade" id="following-modal" ref="following-modal" tabindex="-1" role="dialog" aria-labelledby="likesModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<div class="w-100 position-relative px-15 py-12 border-bottom border-color-1">
							<div class="font-weight-bolder text-center">Following</div>
							<div class="close position-absolute posr-15 my-auto posy-0" data-dismiss="modal" aria-label="Close"></div>
						</div>
						<div class="follow-box py-7">
							<div class="follow">
								<div v-for="(following, key) in followings" class="media align-items-center px-16 py-8">
									<img class="fw-44 fh-44 mr-12 rounded-half-circle" :src="following.following_user.photo_url">
									<div class="media-body media-body d-flex align-items-center">
										<div class="mr-auto">
											<router-link tag="a" :to="{ name: 'profile', params: { username: following.following_user.username } }" class="username font-weight-bolder text-size-14">{{ following.following_user.username }}</router-link>
											<div class="name font-weight-bold text-color-7">{{ following.following_user.name }}</div>
										</div>
										<button @click="follow(key, following.following_user.id)" v-if="!following.following_user.following" class="btn btn-primary font-weight-bolder fh-30 py-0">Follow</button>
										<button @click="follow(key, following.following_user.id)" v-else class="btn btn-outline-secondary font-weight-bolder border-color-2 text-color-12 fh-30 py-0">Following</button>
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
    			followings: [],
    			onLoadFollowings: false,
				lastFollowings: false
    		}
    	},
    	props: ['user'],
	    methods: {
			getFollowing() {
				let self = this
				self.onLoadFollowings = true
				if (!self.lastFollowings) {
					user.getFollowing({
						userId: this.user.id,
						params: {
							page: self.page
						}
					}, res => {
						if (res.data.length) {
							if (self.followings.length) {
								self.followings = self.followings.concat(res.data)
							} else {
								self.followings = res.data
							}

							self.onLoadFollowings = false
						} else {
							self.lastFollowings = true
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
					this.followings[key].following_user.following = this.followings[key].following_user.following ? 0 : 1

					if (!this.followings[key].following_user.following) {
						this.$store.dispatch('main/setReport', 'You have stopped following @' + this.followings[key].following_user.username + '.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have started following @' + this.followings[key].following_user.username + '.')	
					}
				}, err => {
    				console.log(err)
				})	
			},
			initScrollPagination() {
				let self = this
				let followBox = $(self.$refs['following-modal']).find('.follow-box')
				$(followBox).scroll(function() {
					if (($(followBox).scrollTop() + $(followBox).height() > $(followBox).find('.follow').height() * 0.9) && !self.onLoadFollowings) {
						self.page++;
						self.getFollowing()
					}
				})
			},
			initModal() {
				let self = this
				$('#following-modal').on('show.bs.modal', function() {
					self.page = 1
	    			self.followings = []
	    			self.onLoadFollowings = false
					self.lastFollowings = false
					self.getFollowing()
    				self.initScrollPagination()
				})
			}
	    },
	    mounted() {
	    	this.initModal()
	    }
    }
</script>