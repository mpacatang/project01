<template>
	<section>
		<div class="list-post row">
			<div v-for="post in posts" v-if="!post.deleted" class="col-4">
				<div @click="showPostModal(post)" class="post position-relative mb-4 mb-md-30 cursor-pointer">
					<div class="position-absolute w-100 h-100 post-0 posl-0">
						<img class="position-absolute pos-0 w-100 h-100 object-fit-cover" :src="post.photo_url">
						<div class="overlay d-flex w-100 h-100 justify-content-center align-items-center position-absolute post-0 posl-0">
							<div class="d-md-flex text-center text-md-left font-weight-bolder text-white">
								<div v-if="post.likes_count" class="mx-15">
									<i class="icon icon-heart text-size-15 text-size-sm-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-sm-20">{{ post.likes_count }}</span>
								</div>
								<div class="mx-15">
									<i class="icon icon-bubbles text-size-15 text-size-sm-19 mr-3" aria-hidden="true"></i> 
									<span class="text-size-16 text-size-sm-20">{{ post.comments_count }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<post-modal :modalClass="'thumbnail-post-modal'" :post="postModal"></post-modal>
	</section>
</template>

<script>
	import PostModal from './PostModal'

    export default {
    	data() {
    		return {
    			postModal: []
    		}
    	},
    	props: ['posts'],
    	watch: {
			'$route'(to, from) {
    			this.postModal = []
			}
		},
        methods: {
        	showPostModal(post) {
				this.postModal = post
				$('#thumbnail-post-modal').modal('show')
			}
        },
    	components: {
			'post-modal': PostModal
		}
    }
</script>