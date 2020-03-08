<template>
	<section>
		<div v-if="Object.keys(userProfile).length" class="main-profile position-relative pb-80 pb-md-0 px-15 px-md-0">
			<div class="row">
				<div class="col-3 col-sm-2 col-md-4 text-md-center">
					<img @click="showStoryModal" :class="getProfileClass()" :src="userProfile.photo_url">
				</div>
				<div class="user-info col-9 col-sm-10 col-md-8">
					<div class="d-md-flex align-items-end mt-8 mt-md-10 ml-20 ml-sm-15 ml-md-0">
						<div class="d-flex align-items-end">
							<span class="user-name text-size-26 text-size-lg-28 lh-33">{{ userProfile.username }}</span>
							<a v-if="userProfile.id == user.id" class="ml-10 ml-md-195" data-toggle="modal" data-target="#profile-modal" href="javascript:;"><i class="icon icon-settings text-size-20 lh-20" aria-hidden="true"></i></a>
							<router-link v-else tag="a" :to="{ name: 'message-details', params: { username: userProfile.username } }" class="ml-10 ml-md-195" href="javascript:;"><i class="icon icon-bubbles text-size-20 lh-20" aria-hidden="true"></i></router-link>
						</div>
						<router-link tag="button" v-if="userProfile.id == user.id" :to="{ name: 'edit-profile' }" class="btn btn-outline-secondary font-weight-bolder border-color-16 text-color-12 fw-160 fh-30 py-0 mml-md-195 mt-18 mt-md-0">Edit Profile</router-link>
						<template v-else>
							<button v-if="userProfile.following" @click="follow(userProfile.id)" class="btn btn-outline-secondary font-weight-bolder border-color-16 text-color-12 fw-160 fh-30 py-0 mml-md-195 mt-18 mt-md-0">Following</button>
							<button v-else @click="follow(userProfile.id)" class="btn btn-primary font-weight-bolder fw-160 fh-30 py-0 mml-md-195 mt-18 mt-md-0">Follow</button>
						</template>
					</div>
					<div class="w-100 d-flex justify-content-between justify-content-md-start mt-25 position-absolute posb-0 posl-0 py-10 py-md-0 position-md-relative border-top border-color-1 border-color-md-4">
						<div class="mr-md-35 text-center text-md-left w-100 w-md-auto">
							<span class="d-block d-md-inline text-size-14 text-size-md-17 font-weight-bolder">{{ userProfile.posts_count }}</span> 
							<span class="text-color-7 text-color-md-12 text-size-14 text-size-md-16 mmt-3 d-block d-md-inline">posts</span>
						</div>
						<div @click="showFollowersModal()" class="mr-md-35 text-center text-md-left w-100 w-md-auto cursor-pointer">
							<span class="d-block d-md-inline text-size-14 text-size-md-17 font-weight-bolder">{{ userProfile.followers_count }}</span> 
							<span class="text-color-7 text-color-md-12 text-size-14 text-size-md-16 mmt-3 d-block d-md-inline">followers</span>
						</div>
						<div @click="showFollowingModal()" class="mr-md-35 text-center text-md-left w-100 w-md-auto cursor-pointer">
							<span class="d-block d-md-inline text-size-14 text-size-md-17 font-weight-bolder">{{ userProfile.follows_count }}</span> 
							<span class="text-color-7 text-color-md-12 text-size-14 text-size-md-16 mmt-3 d-block d-md-inline">following</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-8 ml-md-auto mmt-md-59 mt-15">
					<div class="font-weight-bolder text-size-md-16">{{ userProfile.name }}</div>
					<div class="text-size-md-15 mt-3 lh-24 lh-md-27">{{ userProfile.bio }}</div>
				</div>
			</div>
		</div>
		<profile-modal></profile-modal>
		<followers-modal :user="userProfile"></followers-modal>
		<following-modal :user="userProfile"></following-modal>
		<single-story-modal :modalClass="'main-profile-single-story-modal'" :user="userProfile" @setProfile="setProfile"></single-story-modal>
	</section>
</template>

<script>
	import user from '../../api/user'
	import ProfileModal from './ProfileModal'
    import FollowersModal from './FollowersModal'
    import FollowingModal from './FollowingModal'
    import SingleStoryModal from './SingleStoryModal'
    import { mapGetters, mapActions } from 'vuex'
    
    export default {
    	data() {
    		return {
    			userProfile: []
    		}
    	},
    	computed: mapGetters({
            user: 'main/user'
        }),
    	watch: {
			'$route'(to, from) {
    			this.userProfile = []
				this.getSingleUser()
			}
		},
        methods: {
        	getSingleUser() {
				user.getSingleUser({
					username: this.$route.params.username
				}, res => {
					res.stories = res.stories.map(function(story, key) {
						story.animate = false
						story.progressBarAnimation = 0
						return story
					})

					this.userProfile = res
				}, err => {
    				console.log(err)
				})
			},
        	getProfileClass() {
        		let profileClass = {
        			'fw-95': true,
        			'fw-md-170':  true,
        			'cursor-pointer': true,
        			'with-stories-active': this.userProfile.stories.length && !this.userProfile.viewed_all_stories,
        			'with-stories': this.userProfile.stories.length && this.userProfile.viewed_all_stories
        		}

        		return profileClass
        	},
        	follow(userId) {
				user.follow({
					userId: userId
				}, res => {
					this.userProfile.following = this.userProfile.following ? 0 : 1

					if (!this.userProfile.following) {
						this.$store.dispatch('main/setReport', 'You have stopped following @' + this.userProfile.username + '.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have started following @' + this.userProfile.username + '.')	
					}
				}, err => {
    				console.log(err)
				})
			},
        	showStoryModal() {
        		if (this.userProfile.stories.length) {
					$('#main-profile-single-story-modal').modal('show')
        		}
        	},
        	setProfile(data) {
        		this.userProfile[data.key] = data.value
        	},
        	showFollowersModal() {
				$('#followers-modal').modal('show')
			},
			showFollowingModal() {
				$('#following-modal').modal('show')
			}
        },
    	components: {
			'profile-modal': ProfileModal,
			'followers-modal': FollowersModal,
			'following-modal': FollowingModal,
			'single-story-modal': SingleStoryModal
		},
		mounted() {
			this.getSingleUser()
		}
    }
</script>