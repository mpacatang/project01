<template>
	<div class="px-10 py-5">
		<template v-if="step == 'activating-account'">Activating account, please wait...</template>
		<template v-else>This activation link is expired or have already been used</template>
	</div>
</template>

<script>
	import moment from 'moment'
	import auth from '../../api/auth'

	export default {
		data() {
			return {
				step: 'activating-account'
			}
		},
		methods: {
			activateAccount() {
				let self = this
				auth.activateAccount({
					activation_code: this.$route.params.activationCode
				}, data => {
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
    				self.step = 'activation-finished'
				})
			}
		},
		mounted() {
			this.activateAccount()
		}
    }
</script>