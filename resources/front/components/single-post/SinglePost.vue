<template>
	<section>
		<div v-if="Object.keys(post).length" class="full-width-container container mt-85 mt-lg-120 px-0 px-md-20 pb-50 pb-lg-0">
			<div ref="single-post" class="post landscape mx-auto bg-white border border-color-1 rounded mb-60 row position-relative">
				<div class="col-12 col-lg-4 py-15 py-lg-0 px-lg-25 position-lg-absolute posr-lg-0 post-lg-20 z-index-1">
					<div class="media align-items-center border-bottom border-color-9 border-color-lg-6 pb-lg-17">
						<img class="mr-13 fw-30 fw-lg-40 rounded-half-circle" :src="post.user.photo_url">
						<div class="media-body">
							<router-link tag="a" :to="{ name: 'profile', params: { username: post.user.username } }" class="font-weight-bolder">{{ post.user.username }}</router-link>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-8 p-0 position-lg-relative">
					<img class="main-image w-100 h-lg-100 object-fit-cover position-lg-absolute" :src="post.photo_url">
				</div>
				<div class="col-12 col-lg-4 p-15 px-lg-0 position-relative">
					<div class="position-lg-absolute w-100 posb-lg-83 px-lg-25">
						<div class="d-flex pt-lg-10 border-top border-color-9 border-color-lg-6">
							<a @click="like()" :class="{ 'mr-18': true, 'text-danger': post.liked }" href="javascript:;"><i class="icon text-size-25 icon-heart" aria-hidden="true"></i></a>
							<a @click="focusComment()" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-bubbles" aria-hidden="true"></i></a>
							<a @click="showShareModal()" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-share-alt" aria-hidden="true"></i></a>
							<a @click="savePost()" :class="{ 'ml-auto': true, 'text-primary': post.saved }" href="javascript:;"><i class="icon text-size-25 icon-flag" aria-hidden="true"></i></a>
						</div>
						<div v-if="post.likes_count" @click="showLikesModal()" class="font-weight-bolder cursor-pointer mt-5 mt-lg-3">{{ post.likes_count }} likes</div>
						<div v-else class="mt-5 mt-lg-3">Be the first to <span @click="like()" class="cursor-pointer font-weight-bolder">like this</span></div>
					</div>
					<div class="comments position-lg-absolute px-lg-25 post-lg-78 pt-lg-3 pb-lg-10">
						<div v-if="post.caption !== null" class="mt-5">
							<router-link tag="a" :to="{ name: 'profile', params: { username: post.user.username } }" class="font-weight-bolder">{{ post.user.username }}</router-link> {{ post.caption }}
						</div>
						<a v-if="!post.showAllComments && post.comments.length > 3" @click="showAllComments()" class="d-block font-weight-bolder text-color-7 mt-5" href="javascript:;">Load more comments</a>
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
					</div>
					<div class="position-lg-absolute w-100 posb-lg-10 posr-lg-0 px-lg-25">
						<div class="mt-7 font-weight-bolder text-color-7 text-size-11 text-uppercase">{{ timeAgo(post.created_at) }}</div>
						<div class="position-relative border-top border-color-6 mt-10 mt-lg-5">
							<textarea v-model="post.comment" @keyup.enter="postComment()" type="text" class="form-control fh-35 w-96 mt-10 rounded-0 border-0 pl-0 resize-none" placeholder="Add a comment..."></textarea>
							<a @click="showOptionModal()" class="position-absolute posr-0 post-19" href="javascript:;">
								<i class="icon-options text-size-15"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<post-option-modal :modalClass="'single-post-option-modal'" :post="postOptionModal.post"></post-option-modal>
		<share-post-modal :modalClass="'single-post-share-post-modal'" :post="sharePostModal.post"></share-post-modal>
	</section>
</template>

<script>
	import post from '../../api/post'
	import helpers from '../../helpers'
	import PostOptionModal from '../feed/PostOptionModal'
	import SharePostModal from '../feed/SharePostModal'
    import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			post: [],
    			postOptionModal: {
    				post: []
    			},
    			sharePostModal: {
    				post: []
    			}
    		}
    	},
    	computed: mapGetters({
            user: 'main/user'
        }),
        methods: {
        	getSinglePost() {
        		post.getSinglePost({
					slug: this.$route.params.slug
				}, res => {
					res.showAllComments = false
					res.newComments = []
					res.comment = ''
					this.post = res
				}, err => {
    				console.log(err)
				})
        	},
        	showAllComments() {
				this.post.showAllComments = true
			},
			postComment() {
				post.postComment({
					postId: this.post.id,
					params: {
						comment: this.post.comment
					}
				}, res => {
					res.user = this.user
					this.post.newComments.push(res)
					this.post.comment = ''
				}, err => {
    				console.log(err)
				})
			},
			like() {
				post.like({
					postId: this.post.id
				}, res => {
					this.post.liked = this.post.liked ? 0 : 1
					this.post.likes_count = this.post.liked ? this.post.likes_count + 1 : this.post.likes_count - 1
				}, err => {
    				console.log(err)
				})
			},
			savePost() {
				post.savePost({
					postId: this.post.id
				}, res => {
					this.post.saved = this.post.saved ? 0 : 1

					if (!this.post.saved) {
						this.$store.dispatch('main/setReport', 'You have deleted @' + this.post.user.username + ' post from your saved post.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have saved @' + this.post.user.username + ' post.')	
					}
				}, err => {
    				console.log(err)
				})
			},
			timeAgo(time) {
				return helpers.timeAgo(time)
			},
			showLikesModal() {
				this.$store.dispatch('likesModal/setPostId', this.post.id)
				this.$store.dispatch('likesModal/getLikes')
				$('#likes-modal').modal('show')
			},
			showOptionModal() {
				$('#single-post-option-modal').modal('show')
				this.postOptionModal.post = this.post
			},
			showShareModal(post) {
				$('#single-post-share-post-modal').modal('show')	
				this.sharePostModal.post = this.post
			},
			focusComment() {
				$(this.$refs['single-post']).find('.form-control').focus()
			}
        },
        components: {
            'post-option-modal': PostOptionModal,
            'share-post-modal': SharePostModal
        },
        mounted() {
        	this.getSinglePost()
        }
    }
</script>