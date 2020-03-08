<template>
	<div class="auth-box">
		<div class="bg-white border border-color-1 rounded p-20 pt-30 rounded text-center fadeInDown animated">
			<div class="logo-box font-weight-bolder text-size-42 lh-42">Yuup</div>
			<form v-if="step == 'reset-password'" class="mt-30" @keyup.enter="forgotPassword">
				<div class="mb-15">If you have forgotten your password, simply enter your email address below.</div>
				<div class="form-input">
					<input type="text" v-model="form.username" class="form-control py-22" placeholder="Enter your email or username">
				</div>
				<button type="button" class="btn btn-primary btn-block mt-25 py-11" @click="forgotPassword" :disabled="btnAction != 'Reset Password' ? true : false">{{ btnAction }}</button>
				<ul v-if="Object.keys(errorMessages).length" class="error-messages pt-20">
					<li v-for="message in errorMessages.username">{{ message }}</li>
				</ul>
			</form>
			<div v-else class="mt-30">
				A reset password email was sent to <span class="text-size-15 font-weight-bolder">{{ form.username }}</span>. Open this email and click the link to reset your password.
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
					username: ''
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
			validateForgotPassword() {
				let errorMessages = {}
				if (this.form.username == '') {
					errorMessages['username'] = ['Please fill the email or username field']
				}

				this.errorMessages = errorMessages
				return Object.keys(errorMessages).length ? false : true
			},
			forgotPassword() {
				let self = this
				self.btnAction = 'Please wait...'

				if (self.validateForgotPassword()) {
					auth.forgotPassword(self.form, data => {
						self.step = 'email-sent'
						self.btnAction = 'Reset Password'
						self.form.username = data.email
					}, err => {
						self.$set(self.errorMessages, 'username', ['The email or username were incorrect.'])
	    				self.btnAction = 'Reset Password'
						self.form.password = ''
					})
				} else {
					self.btnAction = 'Reset Password'
				}
			}
		}
    }
</script>