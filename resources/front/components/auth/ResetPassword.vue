<template>
	<div class="auth-box">
		<div class="bg-white border border-color-1 rounded p-20 pt-30 rounded text-center fadeInDown animated">
			<div class="logo-box font-weight-bolder text-size-42 lh-42">Yuup</div>
			<form v-if="step == 'reset-password'" class="mt-30" @keyup.enter="resetPassword">
				<div class="form-input">
					<input type="password" v-model="form.new_password" class="form-control py-22" placeholder="Enter your new password">
					<input type="password" v-model="form.new_password_confirmation" class="form-control py-22" placeholder="Enter your new password confirmation">
				</div>
				<button type="button" class="btn btn-primary btn-block mt-25 py-11" @click="resetPassword" :disabled="btnAction != 'Reset Password' ? true : false">{{ btnAction }}</button>
				<ul v-if="Object.keys(errorMessages).length" class="error-messages pt-20">
					<li v-for="message in errorMessages.new_password">{{ message }}</li>
					<li v-for="message in errorMessages.new_password_confirmation">{{ message }}</li>
				</ul>
			</form>
			<div v-else class="mt-30">
				Your password has been reset successfully, <router-link tag="a" :to="{ name: 'login' }" class="text-primary">click here</router-link> to login to your account.
			</div>
		</div>
		<div class="bg-white border border-color-1 rounded p-20 rounded text-center fadeInDown animated mt-20">
			Already have an account? <router-link tag="a" :to="{ name: 'login' }" class="text-primary">Sign in</router-link>
		</div>
		<footer-link></footer-link>
	</div>
</template>

<script>
	import auth from '../../api/auth'
	import FooterLink from './FooterLink'

	export default {
		data() {
			return {
				form: {
					reset_code: '',
					new_password: '',
					new_password_confirmation: ''
				},
				step: 'reset-password',
				btnAction: 'Reset Password',
				errorMessages: []
			}
		},
		components: {
        	'footer-link': FooterLink
        },
		methods: {
			validateResetPassword() {
				let errorMessages = {}
				if (this.form.new_password == '') {
					errorMessages['new_password'] = ['Please fill the new password field']
				}

				if (this.form.new_password_confirmation == '') {
					errorMessages['new_password_confirmation'] = ['Please fill the new password confirmation field']
				}

				this.errorMessages = errorMessages
				return Object.keys(errorMessages).length ? false : true
			},
			resetPassword() {
				let self = this
				self.btnAction = 'Please wait...'

				if (self.validateResetPassword()) {
					auth.resetPassword(self.form, data => {
						self.step = 'password-reset'
						self.btnAction = 'Reset Password'
					}, err => {
						self.errorMessages = err.response.data.errors
	    				self.btnAction = 'Reset Password'
					})
				} else {
					self.btnAction = 'Reset Password'
				}
			}
		},
		mounted() {
			this.form.reset_code = this.$route.params.resetCode
		}
    }
</script>