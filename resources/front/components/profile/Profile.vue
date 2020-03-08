<template>
	<div class="full-width-container container mt-85 mt-lg-120 px-0 px-md-20 pb-50 pb-lg-0">
		<main-profile></main-profile>
		<template v-if="posts.length">
			<ul class="profile-post-nav nav nav-tabs justify-content-center mt-md-50">
				<li @click="setActiveTab('thumbnail')" class="nav-item mmt-1 mx-sm-25">
					<a class="nav-link rounded-0 font-weight-bolder py-14 text-size-13 active" id="thumbnail-tab" data-toggle="tab" href="#thumbnail" role="tab" aria-controls="thumbnail" aria-selected="true">
						<i class="icon icon-grid text-size-12 pr-3" aria-hidden="true"></i> THUMBNAIL
					</a>
				</li>
				<li @click="setActiveTab('list')" class="nav-item mmt-1 mx-sm-25">
					<a class="nav-link rounded-0 font-weight-bolder py-14 text-size-13" id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="true">
						<i class="icon icon-list text-size-12 pr-3" aria-hidden="true"></i> LIST
					</a>
				</li>
				<li @click="setActiveTab('saved')" v-if="user.username == $route.params.username" class="nav-item mmt-1 mx-sm-25">
					<a class="nav-link rounded-0 font-weight-bolder py-14 text-size-13" id="saved-tab" data-toggle="tab" href="#saved" role="tab" aria-controls="saved" aria-selected="true">
						<i class="icon icon-flag text-size-12 pr-3" aria-hidden="true"></i> SAVED
					</a>
				</li>
			</ul>
			<div class="tab-content mt-5" id="tab-content">
				<div class="tab-pane fade show active" id="thumbnail" role="tabpanel" aria-labelledby="thumbnail-tab">
					<thumbnail-posts @getUserPosts="getUserPosts" :posts="posts"></thumbnail-posts>
				</div>
				<div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
					<list-posts @getUserPosts="getUserPosts" :posts="posts"></list-posts>
				</div>
				<div v-if="user.username == $route.params.username" class="tab-pane fade" id="saved" role="tabpanel" aria-labelledby="saved-tab">
					<saved-posts :activeTab="activeTab"></saved-posts>
				</div>
			</div>
		</template>
	</div>
</template>

<script>
	import post from '../../api/post'
    import MainProfile from './MainProfile'
    import ListPosts from './ListPosts'
    import ThumbnailPosts from './ThumbnailPosts'
    import SavedPosts from './SavedPosts'
    import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			page: 1,
    			posts: [],
    			onLoadPosts: false,
				lastPosts: false,
				activeTab: 'thumbnail'
    		}
    	},
    	computed: mapGetters({
            user: 'main/user'
        }),
    	watch: {
			'$route'(to, from) {
				this.page = 1
    			this.posts = []
    			this.onLoadPosts = false
				this.lastPosts = false
				this.getUserPosts()
				this.hideActiveModals()
			}
		},
        methods: {
        	getUserPosts() {
				let self = this
				self.onLoadPosts = true
				if (!self.lastPosts) {
					post.getUserPosts({
						username: self.$route.params.username,
						params: {
							page: self.page
						}
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
					if (($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) && !self.onLoadPosts && self.activeTab != 'saved' && self.$route.name == 'profile') {
						self.page++;
						self.getUserPosts()
					}
				})
			},
			hideActiveModals() {
				$('.post-modal').modal('hide')
				$('.likes-modal').modal('hide')
				$('.follow-modal').modal('hide')
				$('.story-modal').modal('hide')
			},
			setActiveTab(tab) {
				this.activeTab = tab
			}
        },
    	components: {
			'main-profile': MainProfile,
			'list-posts': ListPosts,
			'thumbnail-posts': ThumbnailPosts,
			'saved-posts': SavedPosts
		},
		mounted() {
			this.getUserPosts()
			this.hideActiveModals()
			this.initScrollPagination()
		}
    }
</script>