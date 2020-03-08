<template>
	<div class="container mt-120">
		<div class="mb-30">
			<span class=" font-weight-bolder text-size-25 lh-20 mr-10">#Explore Posts</span> <span>Explore tranding photos and videos</span>
		</div>
		<div v-if="posts.length" class="list-post row sm-gutter">
			<div class="col-6">
				<div @click="showPostModal(posts[0])" class="post position-relative mb-4 mb-md-10 cursor-pointer">
					<div class="position-absolute w-100 h-100 post-0 posl-0">
						<img class="position-absolute pos-0 w-100 h-100 object-fit-cover" :src="posts[0].photo_url">
						<div class="overlay d-flex w-100 h-100 justify-content-center align-items-center position-absolute post-0 posl-0">
							<div class="d-lg-flex text-center text-md-left font-weight-bolder text-white">
								<div class="mx-15">
									<i class="icon icon-heart text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-md-20">{{ posts[0].likes_count }}</span>
								</div>
								<div class="mx-15">
									<i class="icon icon-bubbles text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-md-20">{{ posts[0].comments_count }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="row sm-gutter">
					<div @click="showPostModal(post)" v-for="post in posts.slice(1, 5)" class="col-6">
						<div class="post position-relative mb-4 mb-md-10 cursor-pointer">
							<div class="position-absolute w-100 h-100 post-0 posl-0">
								<img class="position-absolute pos-0 w-100 h-100 object-fit-cover" :src="post.photo_url">
								<div class="overlay d-flex w-100 h-100 justify-content-center align-items-center position-absolute post-0 posl-0">
									<div class="d-lg-flex text-center text-md-left font-weight-bolder text-white">
										<div class="mx-15">
											<i class="icon icon-heart text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
											<span class="text-size-16 text-size-md-20">{{ post.likes_count }}</span>
										</div>
										<div class="mx-15">
											<i class="icon icon-bubbles text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
											<span class="text-size-16 text-size-md-20">{{ post.comments_count }}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div v-for="post in posts.slice(5)" class="col-4 col-md-3">
				<div @click="showPostModal(post)" class="post position-relative mb-4 mb-md-10 cursor-pointer">
					<div class="position-absolute w-100 h-100 post-0 posl-0">
						<img class="position-absolute pos-0 w-100 h-100 object-fit-cover" :src="post.photo_url">
						<div class="overlay d-flex w-100 h-100 justify-content-center align-items-center position-absolute post-0 posl-0">
							<div class="d-lg-flex text-center text-md-left font-weight-bolder text-white">
								<div class="mx-15">
									<i class="icon icon-heart text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-md-20">{{ post.likes_count }}</span>
								</div>
								<div class="mx-15">
									<i class="icon icon-bubbles text-size-15 text-size-md-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-md-20">{{ post.comments_count }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<post-modal :modalClass="'thumbnail-post-modal'" :post="postModal"></post-modal>
	</div>
</template>

<script>
	import post from '../../api/post'
	import PostModal from '../profile/PostModal'
    import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			page: 1,
    			posts: [],
    			postModal: [],
    			onLoadPosts: false,
				lastPosts: false
    		}
    	},
    	computed: mapGetters({
	    	user: 'main/user'
        }),
        watch: {
			'$route'(to, from) {
    			this.postModal = []
			}
		},
        methods: {
        	getExplorePosts() {
				let self = this
				self.onLoadPosts = true
				if (!self.lastPosts) {
					post.getExplorePosts({
						page: self.page
					}, res => {
						if (res.data.length) {
							res.data.map(function(post, key) {
			    				post.showAllComments = false
			    				post.newComments = []
			    				post.deleted = false
			    				post.comment = ''
								return post
							})

							if (self.posts.length) {
								self.posts = self.posts.concat(res.data)
							} else {
								self.posts = res.data
							}

							self.onLoadPosts = false
						} else {
							self.lastPosts = true
						}
					}, err => {
	    				console.log(err)
					})
				}
			},
        	initScrollPagination() {
				let self = this
				$(window).scroll(function() {
					if (($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) && !self.onLoadPosts) {
						self.page++;
						self.getExplorePosts()
					}
				})
			},
			showPostModal(post) {
				this.postModal = post
				$('#thumbnail-post-modal').modal('show')
			}
        },
        components: {
			'post-modal': PostModal
		},
		mounted() {
			this.getExplorePosts()
			this.initScrollPagination()
		}
    }
</script>