<template>
	<div class="auth-box">
		<div class="bg-white border border-color-1 rounded p-20 pt-30 rounded text-center fadeInDown animated">
			<div class="logo-box font-weight-bolder text-size-42 lh-42">Yuup</div>
			<form v-if="step == 'resend-activation'" class="mt-30" @keyup.enter="resendActivation">
				<div class="mb-15">Enter your email address and password below to have your activation email resent to you.</div>
				<div class="form-input">
					<input type="text" v-model="form.username" class="form-control py-22" placeholder="Enter your email or username">
					<input type="password" v-model="form.password" class="form-control py-22" placeholder="Enter your password">
				</div>
				<button type="button" class="btn btn-primary btn-block mt-25 py-11" @click="resendActivation" :disabled="btnAction != 'Resend Activation Email' ? true : false">{{ btnAction }}</button>
				<ul v-if="Object.keys(errorMessages).length" class="error-messages pt-20">
					<li v-for="message in errorMessages.username">{{ message }}</li>
					<li v-for="message in errorMessages.password">{{ message }}</li>
				</ul>
			</form>
			<div v-else class="mt-30">
				A verification email was resent to <span class="text-size-15 font-weight-bolder">{{ form.username }}</span>. Open this email and click the link to activate your account.
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
					username: '',
					password: ''
				},
				step: 'resend-activation',
				btnAction: 'Resend Activation Email',
				errorMessages: []
			}
		},
		components: {
        	'footer-link': FooterLink
        },
		methods: {
			validateResendActivation() {
				let errorMessages = {}
				if (this.form.username == '') {
					errorMessages['username'] = ['Please fill the email or username field']
				}

				if (this.form.password == '') {
					errorMessages['password'] = ['Please fill the password field']
				}

				this.errorMessages = errorMessages
				return Object.keys(errorMessages).length ? false : true
			},
			resendActivation() {
				let self = this
				self.btnAction = 'Please wait...'

				if (self.validateResendActivation()) {
					auth.resendActivation(self.form, data => {
						self.step = 'email-sent'
						self.btnAction = 'Resend Activation Email'
						self.form.username = data.email
					}, err => {
						self.$set(self.errorMessages, 'password', ['Sorry, your password was incorrect. Please double-check your password.'])
	    				self.btnAction = 'Resend Activation Email'
						self.form.password = ''
					})
				} else {
					self.btnAction = 'Resend Activation Email'
				}
			}
		}
    }
</script>