<template>
	<section>
		<div class="list-post row">
			<div v-for="post in posts" v-if="post.saved && !post.deleted" class="col-4">
				<div @click="showPostModal(post)" class="post position-relative mb-4 mb-md-30 cursor-pointer">
					<div class="position-absolute w-100 h-100 post-0 posl-0">
						<img class="position-absolute pos-0 w-100 h-100 object-fit-cover" :src="post.photo_url">
						<div class="overlay d-flex w-100 h-100 justify-content-center align-items-center position-absolute post-0 posl-0">
							<div class="d-md-flex text-center text-md-left font-weight-bolder text-white">
								<div v-if="post.likes_count" class="mx-15">
									<i class="icon icon-heart text-size-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-20">{{ post.likes_count }}</span>
								</div>
								<div class="mx-15">
									<i class="icon icon-bubbles text-size-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-20">{{ post.comments_count }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
		<post-modal :modalClass="'saved-post-modal'" :post="postModal"></post-modal>
	</section>
</template>

<script>
	import post from '../../api/post'
	import PostModal from './PostModal'

    export default {
    	data() {
    		return {
    			page: 1,
    			posts: [],
    			onLoadPosts: false,
				lastPosts: false,
    			postModal: []
    		}
    	},
    	props: ['activeTab'],
    	watch: {
			'$route'(to, from) {
				this.page = 1
    			this.posts = []
    			this.onLoadPosts = false
				this.lastPosts = false
    			this.postModal = []
    			this.getSavedPosts()
			}
		},
        methods: {
        	showPostModal(post) {
				this.postModal = post
				$('#saved-post-modal').modal('show')
			},
			getSavedPosts() {
				let self = this
				self.onLoadPosts = true
				if (!self.lastPosts) {
					post.getSavedPosts({
						page: self.page
					}, res => {
						if (res.data.length) {
							res.data.map(function(post, key) {
			    				post.showAllComments = false
			    				post.newComments = []
			    				post.deleted = false
			    				post.comment = []
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
					if (($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) && !self.onLoadPosts && self.activeTab == 'saved') {
						self.page++;
						self.getSavedPosts()
					}
				})
			}
        },
    	components: {
			'post-modal': PostModal
		},
		mounted() {
			this.getSavedPosts()
			this.initScrollPagination()
		}
    }
</script>