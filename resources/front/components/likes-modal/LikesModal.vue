<template>
	<div class="likes-modal modal fade" id="likes-modal" ref="likes-modal" tabindex="-1" role="dialog" aria-labelledby="likesModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<div class="w-100 position-relative px-15 py-12 border-bottom border-color-1">
							<div class="font-weight-bolder text-center">Likes</div>
							<div class="close position-absolute posr-15 my-auto posy-0" data-dismiss="modal" aria-label="Close"></div>
						</div>
						<div class="likes-box py-7">
							<div class="likes">
								<div v-for="(like, key) in likes" class="media align-items-center px-16 py-8">
									<img class="fw-44 fh-44 mr-12 rounded-half-circle" :src="like.user.photo_url">
									<div class="media-body media-body d-flex align-items-center">
										<div class="mr-auto">
											<router-link tag="a" :to="{ name: 'profile', params: { username: like.user.username } }" class="username font-weight-bolder text-size-14">{{ like.user.username }}</router-link>
											<div class="name font-weight-bold text-color-7">{{ like.user.name }}</div>
										</div>
										<template v-if="like.user.id !== user.id">
											<button @click="follow(key, like.user.id)" v-if="!like.user.following" class="btn btn-primary font-weight-bolder fh-30 py-0">Follow</button>
											<button @click="follow(key, like.user.id)" v-else class="btn btn-outline-secondary font-weight-bolder border-color-2 text-color-12 fh-30 py-0">Following</button>
										</template>
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
    	computed: mapGetters({
    		user: 'main/user',
    		page: 'likesModal/page',
            likes: 'likesModal/likes',
            onLoadLikes: 'likesModal/onLoadLikes'
        }),
	    methods: {
			follow(key, userId) {
				user.follow({
					userId: userId
				}, res => {
					this.likes[key].user.following = this.likes[key].user.following ? 0 : 1
				}, err => {
    				console.log(err)
				})	
			},
			initScrollPagination() {
				let self = this
				let likesBox = $(self.$refs['likes-modal']).find('.likes-box')
				$(likesBox).scroll(function() {
					if (($(likesBox).scrollTop() + $(likesBox).height() > $(likesBox).find('.likes').height() * 0.9) && !self.onLoadLikes) {
						self.$store.dispatch('likesModal/setPage', self.page + 1)
						self.$store.dispatch('likesModal/getLikes')
					}
				})
			}
	    },
	    mounted() {
	    	this.initScrollPagination()
	    }
    }
</script>