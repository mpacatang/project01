<template>
	<div class="d-none d-lg-block bg-white mt-20 p-15 border border-color-1 rounded">
		<div class="d-flex w-100 justify-content-between">
			<div class="font-weight-bold text-color-7">Suggestions For You</div>
			<router-link tag="a" :to="{ name: 'suggestions' }" class="font-weight-bolder text-size-13">See All</router-link>
		</div>
		<div v-for="(user, key) in suggestedPeople" :class="getUserClass(user)">
			<img @click="showStoryModal(user)" class="cursor-pointer mr-13 fw-42 rounded-half-circle" :src="user.photo_url">
			<div class="media-body d-flex align-items-center">
				<div class="mr-auto">
					<router-link tag="div" :to="{ name: 'profile', params: { username: user.username } }" class="font-weight-bolder cursor-pointer">{{ user.username }}</router-link>
					<div class="font-weight-bold text-color-7 text-size-12 mt-1">{{ user.name }}</div>
				</div>
				<a v-if="!user.following" @click="follow(key, user.id)" class="font-weight-bolder cursor-pointer text-size-12 text-primary" href="javascript:;">Follow</a>
				<a v-else @click="follow(key, user.id)" class="font-weight-bolder cursor-pointer text-size-12" href="javascript:;">Following</a>
			</div>
		</div>
		<single-story-modal :modalClass="'suggestion-single-story-modal'" :user="userProfile" @setProfile="setProfile"></single-story-modal>
	</div>
</template>

<script>
	import user from '../../api/user'
    import { mapGetters, mapActions } from 'vuex'
    import SingleStoryModal from '../profile/SingleStoryModal'

    export default {
    	data() {
    		return {
    			userProfile: [],
    			suggestedPeople: []
    		}
    	},
    	props: ['user'],
	    methods: {
			getSuggestedPeople() {
				user.getSuggestedPeople(res => {
					res = res.map(function(user, key) {
						user.stories = user.stories.map(function(story, key) {
							story.animate = false
							story.progressBarAnimation = 0
							return story
						})

						return user
					})

					this.suggestedPeople = res
				}, err => {
    				console.log(err)
				})
			},
			follow(key, userId) {
				user.follow({
					userId: userId
				}, res => {
					this.suggestedPeople[key].following = this.suggestedPeople[key].following ? 0 : 1

					if (!this.suggestedPeople[key].following) {
						this.$store.dispatch('main/setReport', 'You have stopped following @' + this.suggestedPeople[key].username + '.')	
					} else {
						this.$store.dispatch('main/setReport', 'You have started following @' + this.suggestedPeople[key].username + '.')	
					}
				}, err => {
    				console.log(err)
				})
			},
			getUserClass(user) {
				return {
					'with-stories': user.viewed_all_stories,
					'with-stories-active': !user.viewed_all_stories,
					'small-profile': true,
					'mt-10': true,
					'media': true,
					'align-items-center': true
				}
			},
			showStoryModal(user) {
        		if (user.stories.length) {
        			this.userProfile = user
        			this.$nextTick(() => {
        				$('#suggestion-single-story-modal').modal('show')	
        			})
        		}
        	},
			setProfile(data) {
        		this.userProfile[data.key] = data.value
        	}
	    },
    	components: {
			'single-story-modal': SingleStoryModal
		},
	    mounted() {
    		this.getSuggestedPeople()
	    }
    }
</script>