<template>
	<div class="px-10 py-5">Logging in, please wait...</div>
</template>

<script>
	import moment from 'moment'
	import auth from '../../api/auth'

	export default {
		methods: {
			getAuthenticatedUser() {
				let self = this
				auth.getAuthenticatedUser(data => {
					if (Object.keys(data).length) {
						data.login_time = moment(new Date())
						localStorage.setItem('token', JSON.stringify(data))
					}

					setTimeout(() => {
						self.$router.push({
							name: 'feed'
						})
					}, 3000)
				}, err => {
    				console.log(err)
				})
			}
		},
		mounted() {
			this.getAuthenticatedUser()
		}
    }
</script>