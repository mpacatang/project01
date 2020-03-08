<template>
    <section v-if="Object.keys(userProfile).length">
    	<div :class="getProfileClass()">
    		<img @click="showStoryModal" class="mr-13 fw-50 rounded-half-circle cursor-pointer" :src="userProfile.photo_url">
    		<div class="media-body">
    			<router-link tag="div" :to="{ name: 'profile', params: { username: userProfile.username } }" class="font-weight-bolder cursor-pointer">{{ userProfile.username }}</router-link>
    			<div class="font-weight-bold text-color-5 text-size-12">{{ userProfile.name }}</div>
    		</div>
    	</div>
        <single-story-modal :modalClass="'profile-single-story-modal'" :user="userProfile" @setProfile="setProfile"></single-story-modal>
    </section>
</template>

<script>
    import SingleStoryModal from '../profile/SingleStoryModal'
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
            user() {
                this.userProfile = this.user
            }
        },
        methods: {
        	getProfileClass() {
        		let profileClass = {
        			'd-none': true,
        			'd-lg-flex':  true,
        			'media':  true,
        			'align-items-center': true,
        			'small-profile': true,
                    'with-stories-active': this.userProfile.stories.length && !this.userProfile.viewed_all_stories,
                    'with-stories': this.userProfile.stories.length && this.userProfile.viewed_all_stories
        		}

        		return profileClass
        	},
            showStoryModal() {
                if (this.userProfile.stories.length) {
                    $('#profile-single-story-modal').modal('show')  
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
            this.userProfile = this.user
        }
    }
</script>