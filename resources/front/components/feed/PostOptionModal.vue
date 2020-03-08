<template>
	<div :class="getPostOptionModalClass()" :id="modalClass" ref="post-option-modal" tabindex="-1" role="dialog" aria-labelledby="postOptionModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div v-if="Object.keys(post).length" class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded overflow-hidden position-relative">
						<template v-if="step == 'default'">
							<a v-if="user.id != post.user.id && post.user.following" @click="setStep('unfollow')" class="w-100 d-block text-center py-10 border-bottom border-color-1 text-danger font-weight-bold" href="javascript:;">Unfollow</a>
							<a v-if="user.id == post.user.id" @click="setStep('delete-post')" class="w-100 d-block text-center py-10 border-bottom border-color-1 text-danger font-weight-bold" href="javascript:;">Delete post</a>
							<a @click="link({ name: 'single-post', params: { slug: post.slug } })" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Go to post</a>
							<a @click="setStep('share')" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Share</a>
							<a @click="copyLink" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Copy Link</a>
							<a class="w-100 d-block text-center py-10" data-dismiss="modal" aria-label="Close" href="javascript:;">Close</a>
						</template>
						<template v-else-if="step == 'unfollow'">
							<div class="text-center">
								<img class="fw-90 mt-35 rounded-half-circle cursor-pointer" :src="post.user.photo_url">
								<div class="mt-20">Unfollow @{{ post.user.username }}?</div>
								<a @click="unfollow" class="mt-25 w-100 d-block text-center py-10 border-top border-bottom border-color-1 text-danger font-weight-bold" href="javascript:;">Unfollow</a>
								<a @click="setStep('default')" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Back</a>
								<a class="w-100 d-block text-center py-10" data-dismiss="modal" aria-label="Close" href="javascript:;">Close</a>
							</div>
						</template>
						<template v-else-if="step == 'delete-post'">
							<div class="pt-35">
								<div class="position-relative fw-90 fh-80 mx-auto">
									<img class="position-absolute w-100 h-100 object-fit-cover rounded" :src="post.photo_url">
								</div>
							</div>
							<div class="text-center">
								<div class="mt-20">Delete this post?</div>
								<a @click="deletePost" class="mt-25 w-100 d-block text-center py-10 border-top border-bottom border-color-1 text-danger font-weight-bold" href="javascript:;">Delete</a>
								<a @click="setStep('default')" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Don't delete</a>
								<a class="w-100 d-block text-center py-10" data-dismiss="modal" aria-label="Close" href="javascript:;">Close</a>
							</div>
						</template>
						<template v-else-if="step == 'share'">
							<div class="w-100 position-relative px-15 py-12 border-bottom border-color-1">
								<div class="font-weight-bolder text-center text-primary">Share</div>
								<div class="close position-absolute posr-15 my-auto posy-0" data-dismiss="modal" aria-label="Close"></div>
							</div>
							<a @click="shareTo('facebook')" class="w-100 d-block mt-15 font-weight-bold pl-20 d-flex align-items-center" href="javascript:;">
								<div class="fw-32 fh-32 mr-12 d-flex justify-content-center align-items-center text-white bg-primary rounded-half-circle bg-color-13"><i class="icon text-size-15 icon-social-facebook"></i></div> Share to Facebook
							</a>
							<a @click="shareTo('twitter')" class="w-100 d-block mt-15 font-weight-bold pl-20 d-flex align-items-center" href="javascript:;">
								<div class="fw-32 fh-32 mr-12 d-flex justify-content-center align-items-center text-white bg-primary rounded-half-circle bg-color-14"><i class="icon text-size-15 icon-social-twitter"></i></div> Share to Twitter
							</a>
							<a @click="copyLink" class="w-100 d-block mt-15 font-weight-bold pl-20 d-flex align-items-center" href="javascript:;">
								<div class="fw-32 fh-32 mr-12 d-flex justify-content-center align-items-center text-white bg-primary rounded-half-circle bg-color-15"><i class="icon text-size-15 icon-link"></i></div> Copy Link
							</a>
							<a @click="setStep('default')" class="w-100 d-block mt-20 font-weight-bold pl-64 pb-25 d-flex align-items-center text-primary" href="javascript:;">Back</a>
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import post from '../../api/post'
	import user from '../../api/user'
    import { mapGetters, mapActions } from 'vuex'

    export default {
	    data() {
    		return {
    			step: 'default'
    		}
    	},
    	props: ['post', 'modalClass'],
    	computed: mapGetters({
            user: 'main/user'
        }),
	    methods: {
    		setStep(step) {
    			let self = this
    			setTimeout(() => {
    				self.step = step	
    			}, 100)
    		},
    		unfollow() {
				user.follow({
					userId: this.post.user.id
				}, res => {
					$('.post-option-modal').modal('hide')
					this.post.user.following = this.post.user.following ? 0 : 1
					this.$store.dispatch('main/setReport', 'You have stopped following @' + this.post.user.username + '.')
				}, err => {
    				console.log(err)
				})
			},
			deletePost() {
				post.deletePost({
					postId: this.post.id
				}, data => {
                    this.post.deleted = true
					$('.post-option-modal').modal('hide')
					$('.post-modal').modal('hide')

					if (this.modalClass == 'single-post-option-modal' && this.post.deleted) {
						this.$router.push({ 
							name: 'profile', 
							params: { 
								username: this.user.username
							} 
						})
					}
                }, err => {
                    console.log(err.response.data.errors)
                })
			},
			link(route) {
				$('.post-option-modal').modal('hide')
				$('.post-modal').modal('hide')
				this.$router.push(route)
			},
			initPostOptionModal() {
				let self = this
				$('.post-option-modal').on('show.bs.modal', function() {
					self.step = 'default'
				})
			},
			copyLink() {
				$(this.$refs['post-option-modal']).find('textarea').remove()
				let textarea = $('<textarea></textarea>').addClass([
					'position-absolute', 
					'posml-200'
				]).val(window.location.hostname + '/p/' + this.post.slug)
				$(this.$refs['post-option-modal']).find('.bg-white').append(textarea)
				textarea.select()
				document.execCommand('copy')
				$('.post-option-modal').modal('hide')
				this.$store.dispatch('main/setReport', 'Link copied to clipboard.')
			},
			shareTo(to) {
				let postUrl = window.location.hostname + '/p/' + this.post.slug
				if (to == 'facebook') {
					window.open('https://www.facebook.com/sharer/sharer.php?u=' + postUrl, '_blank');
				} else if (to == 'twitter') {
					window.open('https://twitter.com/intent/tweet?text=' + postUrl, '_blank');
				}
			},
			getPostOptionModalClass() {
				let postOptionModalClass = {}
				postOptionModalClass[this.modalClass] = true
				postOptionModalClass['post-option-modal'] = true
				postOptionModalClass['modal'] = true
				postOptionModalClass['fade'] = true
				return postOptionModalClass
			}
	    },
	    mounted() {
	    	this.initPostOptionModal()
	    }
    }
</script>