<template>
	<div @click="focusSearchBox" :class="{ 'search-box': true, 'active': focus, 'active-mobile': activeMobile }">
		<input v-model="username" @keyup="onTyping" @keyup.enter="endTyping" ref="search-box" type="text" class="form-control" placeholder="Search">
		<div class="placeholder">
			<i class="icon text-size-10 mr-2 icon-magnifier" aria-hidden="true"></i> 
			<span class="text">{{ username.length ? username : 'Search' }}</span>
		</div>
		<div @click="clearUsername" class="cancel-keyword"></div>
		<div v-if="users.length || noResults" class="search-list">
			<div v-for="user in users" @click="link(user)" class="media px-15 py-10 align-items-center cursor-pointer">
				<img class="mr-15 fw-32 rounded-half-circle" :src="user.photo_url">
				<div class="media-body">
					<div class="user-name font-weight-bolder">{{ user.username }}</div>
					<div class="name font-weight-bold text-color-7 mmt-2">{{ user.name }}</div>
				</div>
			</div>
			<div v-if="noResults" class="text-color-7 text-center py-15">No results found.</div>
		</div>
	</div>
</template>

<script>
	import user from '../../api/user'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
			return {
				users: [],
				focus: false,
				username: '',
				typingTimer: '',
				noResults: false
			}
		},
		computed: mapGetters({
            activeMobile: 'searchBox/activeMobile'
        }),
        methods: {
        	onTyping() {
        		let self = this
        		clearTimeout(this.typingTimer)
  				this.typingTimer = setTimeout(function() {
  					self.getUsers()
  				}, 700)
			},
			endTyping() {
				clearTimeout(this.typingTimer)
				this.getUsers()
			},
			getUsers() {
				this.noResults = false
				if (this.username.length) {
					user.getUsers({
						username: this.username
					}, res => {
						this.users = res
						this.noResults = res.length ? false : true
					}, err => {
	    				console.log(err)
					})
				} else {
					this.users = []
				}
			},
			clearUsername() {
				this.noResults = false
				this.username = ''
				this.users = []
				this.focus = false
			},
        	focusSearchBox() {
        		this.focus = true
        		this.$refs['search-box'].focus()

        		if (this.username.length) {
        			this.getUsers()
        		}
        	},
        	initSearchBox() {
        		let self = this
        		$(document).on('click', function(event) {
        			if ((self.focus && !$(event.target).closest('.search-box').length) || (self.focus && $(event.target).hasClass('cancel-keyword'))) {
						self.noResults = false
						self.users = []
						self.focus = false
					}
        		})
        	},
        	link(user) {
        		this.clearUsername()
        		this.$router.push({ name: 'profile', params: { username: user.username } })
        	}
        },
        mounted() {
            this.initSearchBox()
        }
    }
</script>