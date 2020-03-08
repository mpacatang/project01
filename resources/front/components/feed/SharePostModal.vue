<template>
	<div :class="getSharePostModalClass()" :id="modalClass" ref="share-post-modal" tabindex="-1" role="dialog" aria-labelledby="postOptionModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div v-if="Object.keys(post).length" class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded overflow-hidden position-relative">
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
						<a class="w-100 d-block mt-20 font-weight-bold pl-64 pb-25 d-flex align-items-center text-primary" data-dismiss="modal" aria-label="Close" href="javascript:;">CLose</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
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
			copyLink() {
				$(this.$refs['share-post-modal']).find('textarea').remove()
				let textarea = $('<textarea></textarea>').addClass([
					'position-absolute', 
					'posml-200'
				]).val(window.location.hostname + '/p/' + this.post.slug)
				$(this.$refs['share-post-modal']).find('.bg-white').append(textarea)
				textarea.select()
				document.execCommand('copy')
				$('.share-post-modal').modal('hide')
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
			getSharePostModalClass() {
				let sharePostModalClass = {}
				sharePostModalClass[this.modalClass] = true
				sharePostModalClass['share-post-modal'] = true
				sharePostModalClass['modal'] = true
				sharePostModalClass['fade'] = true
				return sharePostModalClass
			}
	    }
    }
</script>