<template>
	<section>
		<div v-for="(post, key) in posts" :ref="'post-' + post.id" v-if="!post.deleted" class="post landscape mx-auto bg-white border border-color-1 rounded mb-60 row position-relative">
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
						<a @click="like(key, post.id)" :class="{ 'mr-18': true, 'text-danger': post.liked }" href="javascript:;"><i class="icon text-size-25 icon-heart" aria-hidden="true"></i></a>
						<a @click="focusComment(post)" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-bubbles" aria-hidden="true"></i></a>
						<a @click="showShareModal(post)" class="mr-18" href="javascript:;"><i class="icon text-size-25 icon-share-alt" aria-hidden="true"></i></a>
						<a @click="savePost(key, post)" :class="{ 'ml-auto': true, 'text-primary': post.saved }" href="javascript:;"><i class="icon text-size-25 icon-flag" aria-hidden="true"></i></a>
					</div>
					<div v-if="post.likes_count" @click="showLikesModal(post.id)" class="font-weight-bolder cursor-pointer mt-5 mt-lg-3">{{ post.likes_count }} likes</div>
					<div v-else class="mt-5 mt-lg-3">Be the first to <span @click="like(key, post.id)" class="cursor-pointer font-weight-bolder">like this</span></div>
				</div>
				<div class="comments position-lg-absolute px-lg-25 post-lg-78 pt-lg-3 pb-lg-10">
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
				</div>
				<div class="position-lg-absolute w-100 posb-lg-10 posr-lg-0 px-lg-25">
					<div class="mt-7 font-weight-bolder text-color-7 text-size-11 text-uppercase">{{ timeAgo(post.created_at) }}</div>
					<div class="position-relative border-top border-color-6 mt-10 mt-lg-5">
						<textarea v-model="post.comment" @keyup.enter="postComment(key, post)" type="text" class="form-control fh-35 w-96 mt-10 rounded-0 border-0 pl-0 resize-none" placeholder="Add a comment..."></textarea>
						<a @click="showOptionModal(post)" class="position-absolute posr-0 post-19" href="javascript:;">
							<i class="icon-options text-size-15"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<post-option-modal :modalClass="'list-post-option-modal'" :post="postOptionModal.post"></post-option-modal>
		<share-post-modal :modalClass="'list-post-share-post-modal'" :post="sharePostModal.post"></share-post-modal>
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
    			postOptionModal: {
    				post: []
    			},
    			sharePostModal: {
    				post: []
    			}
    		}
    	},
    	props: ['posts'],
    	computed: mapGetters({
            user: 'main/user'
        }),
        methods: {
        	showAllComments(key) {
				this.posts[key].showAllComments = true
			},
			postComment(key, postData) {
				post.postComment({
					postId: postData.id,
					params: {
						comment: postData.comment
					}
				}, res => {
					res.user = this.user
					this.posts[key].newComments.push(res)
					this.posts[key].comment = ''
				}, err => {
    				console.log(err)
				})
			},
			like(key, postId) {
				post.like({
					postId: postId
				}, res => {
					this.posts[key].liked = this.posts[key].liked ? 0 : 1
					this.posts[key].likes_count = this.posts[key].liked ? this.posts[key].likes_count + 1 : this.posts[key].likes_count - 1
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
					
					this.posts[key].saved = this.posts[key].saved ? 0 : 1
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
				$('#list-post-option-modal').modal('show')	
				this.postOptionModal.post = post
			},
			showShareModal(post) {
				$('#list-post-share-post-modal').modal('show')	
				this.sharePostModal.post = post
			},
			focusComment(post) {
				$(this.$refs['post-' + post.id]).find('.form-control').focus()
			}
        },
        components: {
            'post-option-modal': PostOptionModal,
            'share-post-modal': SharePostModal
        },
    }
</script>