<template>
	<div class="border-bottom border-color-1">
		<div class="fh-55 fh-lg-45 px-20 d-flex align-items-center">
			<div class="mr-auto">
				<router-link v-if="Object.keys(user).length" tag="a" :to="{ name: 'profile', params: { username: user.username } }" class="d-block d-lg-inline-block font-weight-bolder">{{ user.name }}</router-link>
				<div class="fw-3 fh-3 bg-color-2 rounded-circle d-none d-lg-inline-block mx-5"></div>
				<span class="d-block d-lg-inline-block text-color-5 text-size-11 text-size-lg-12">Last online 2 hours ago</span>
			</div>
			<i class="icon icon-options-vertical" aria-hidden="true"></i>
		</div>
	</div>
</template>

<script>
	import helpers from '../../helpers'
	import user from '../../api/user'
	import { mapGetters, mapActions } from 'vuex'
	import MessageDetailsHeader from './MessageDetailsHeader'

    export default {
    	data() {
    		return {
    			user: []
    		}
    	},
    	watch: {
    		'$route'(to, from) {
        		this.getUser()
			}
    	},
        methods: {
        	getUser() {
        		let self = this
        		user.getSingleUser({
        			username: this.$route.params.username
        		}, user => {
					self.user = user
				}, err => {
					reject(err)
				})
        	}
        },
        mounted() {
        	this.getUser()	
        }
    }
</script>