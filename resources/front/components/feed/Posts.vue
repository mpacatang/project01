<template>
	<section>
		<template v-if="posts.length">
			<div v-for="(post, key) in posts" :ref="'post-' + post.id" v-if="!post.deleted" class="post mx-auto bg-white border border-color-1 rounded mb-60">
				<div class="media align-items-center p-15">
					<img class="mr-13 fw-30 rounded-half-circle" :src="post.user.photo_url">
					<div class="media-body">
						<router-link tag="a" :to="{ name: 'profile', params: { username: post.user.username } }" class="font-weight-bolder">{{ post.user.username }}</router-link>
					</div>
				</div>
				<img class="w-100" :src="post.photo_url">
				<div class="p-15">
					<div class="d-flex">
						<a @click="like(key, post.id)" :class="{ 'mr-18': true, 'text-danger': post.liked }" href="javascript:;"><i class="icon text-size-25 icon-heart" aria-hidden="true"></i></a>
						<a @click="focusComment(post)" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-bubbles" aria-hidden="true"></i></a>
						<a @click="showShareModal(post)" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-share-alt" aria-hidden="true"></i></a>
						<a @click="savePost(key, post)" :class="{ 'ml-auto': true, 'text-primary': post.saved }" href="javascript:;"><i class="icon text-size-25 icon-flag" aria-hidden="true"></i></a>
					</div>
					<div v-if="post.likes_count" @click="showLikesModal(post.id)" class="font-weight-bolder cursor-pointer mt-5">{{ post.likes_count }} likes</div>
					<div v-else class="mt-5">Be the first to <span @click="like(key, post.id)" class="cursor-pointer font-weight-bolder">like this</span></div>
					<div v-if="post.caption !== null" class="mt-5">
						<router-link tag="a" :to="{ name: 'profile', params: { username: post.user.username } }" class="font-weight-bolder">{{ post.user.username }}</router-link> {{ post.caption }}
					</div>
					<a v-if="!post.showAllComments && post.comments.length > 3" @click="showAllComments(key)" class="d-block font-weight-bolder text-color-7 mt-5" href="javascript:;">Load more comments</a>
					<template v-if="!post.showAllComments">
						<div v-for="(postComment, postCommentKey) in post.comments" v-if="postCommentKey >= post.comments.length - 3" class="mt-5">
							<router-link tag="a" :to="{ name: 'profile', params: { username: postComment.user.username } }" class="font-weight-bolder">{{ postComment.user.username }}</router-link> {{ postComment.comment }}
						</div>
					</template>
					<template v-else>
						<div v-for="(postComment, postCommentKey) in post.comments" class="mt-5">
							<router-link tag="a" :to="{ name: 'profile', params: { username: postComment.user.username } }" class="font-weight-bolder">{{ postComment.user.username }}</router-link> {{ postComment.comment }}
						</div>
					</template>
					<div v-for="postComment in post.newComments" class="mt-5">
						<router-link tag="a" :to="{ name: 'profile', params: { username: postComment.user.username } }" class="font-weight-bolder">{{ postComment.user.username }}</router-link> {{ postComment.comment }}
					</div>
					<div class="mt-7 font-weight-bolder text-color-7 text-size-11 text-uppercase">{{ timeAgo(post.created_at) }}</div>
					<div class="position-relative border-top border-color-6 mt-10">
						<textarea v-model="post.comment" @keyup.enter="postComment(key, post)" type="text" class="form-control fh-35 w-96 mt-10 rounded-0 border-0 pl-0 resize-none" placeholder="Add a comment..."></textarea>
						<a @click="showOptionModal(post)" class="position-absolute posr-0 post-19" href="javascript:;">
							<i class="icon-options text-size-15"></i>
						</a>
					</div>
				</div>
			</div>
		</template>
		<div v-else class="bg-white border border-color-1 rounded p-20 text-center">Your feed will show up here, follow people to see their posts.</div>
		<post-option-modal :modalClass="'feed-post-option-modal'" :post="postOptionModal.post"></post-option-modal>
		<share-post-modal :modalClass="'feed-share-post-modal'" :post="sharePostModal.post"></share-post-modal>
	</section>
</template>

<script>
	import post from '../../api/post'
	import helpers from '../../helpers'
	import PostOptionModal from './PostOptionModal'
	import SharePostModal from './SharePostModal'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		data() {
    		return {
    			page: 1,
    			onLoadPosts: false,
    			postOptionModal: {
    				post: []
    			},
    			sharePostModal: {
    				post: []
    			}
    		}
    	},
    	computed: {
    		...mapGetters({
	            user: 'main/user',
	            posts: 'feed/posts'
        	})
        },
		methods: {
			getPosts() {
				let self = this
				self.onLoadPosts = true
				self.$store.dispatch('feed/getPosts', self.page).then(() => {
					self.onLoadPosts = false
				})
			},
			showAllComments(key) {
				let post = Object.assign({}, this.posts[key])
				post.showAllComments = true
				this.$store.dispatch('feed/modifyPost', {
					key: key,
					post: post
				})
			},
			postComment(key, postData) {
				post.postComment({
					postId: postData.id,
					params: {
						comment: postData.comment
					}
				}, res => {
					this.posts[key].comment = ''
					let post = Object.assign({}, this.posts[key])
					res.user = this.user
					post.newComments.push(res)
					this.$store.dispatch('feed/modifyPost', {
						key: key,
						post: post
					})
				}, err => {
    				console.log(err)
				})
			},
			like(key, postId) {
				post.like({
					postId: postId
				}, res => {
					let post = Object.assign({}, this.posts[key])
					post.liked = post.liked ? 0 : 1
					post.likes_count = post.liked ? post.likes_count + 1 : post.likes_count - 1
					this.$store.dispatch('feed/modifyPost', {
						key: key,
						post: post
					})
				}, err => {
    				console.log(err)
				})
			},
			savePost(key, postData) {
				post.savePost({
					postId: postData.id
				}, res => {
					if (postData.saved) {
						this.$store.dispatch('main/setReport', 'You have deleted @' + postData.user.username + ' post from your saved post.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have saved @' + postData.user.username + ' post.')	
					}

					let post = Object.assign({}, postData)
					post.saved = post.saved ? 0 : 1
					this.$store.dispatch('feed/modifyPost', {
						key: key,
						post: post
					})
				}, err => {
    				console.log(err)
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			},
			showLikesModal(postId) {
				this.$store.dispatch('likesModal/setPostId', postId)
				this.$store.dispatch('likesModal/getLikes')
				$('#likes-modal').modal('show')
			},
			showOptionModal(post) {
				$('#feed-post-option-modal').modal('show')	
				this.postOptionModal.post = post
			},
			showShareModal(post) {
				$('#feed-share-post-modal').modal('show')	
				this.sharePostModal.post = post
			},
			initScrollPagination() {
				let self = this
				$(window).scroll(function() {
					if (($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) && !self.onLoadPosts && self.$route.name == 'feed') {
						self.page++
						self.getPosts()
					}
				})
			},
			focusComment(post) {
				$(this.$refs['post-' + post.id]).find('.form-control').focus()
			}
		},
        components: {
            'post-option-modal': PostOptionModal,
            'share-post-modal': SharePostModal
        },
		mounted() {
			this.getPosts()
			this.initScrollPagination()
		}
    }
</script>