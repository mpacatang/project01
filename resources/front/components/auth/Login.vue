<template>
	<div class="auth-box">
		<div class="bg-white border border-color-1 rounded p-20 pt-30 rounded text-center fadeInDown animated">
			<div class="logo-box font-weight-bolder text-size-42 lh-42">Yuup</div>
			<form v-if="!activation" class="mt-30" @keyup.enter="login">
				<div class="form-input">
					<input type="text" v-model="form.username" class="form-control py-22" placeholder="Enter your email or username">
					<input type="password" v-model="form.password" class="form-control py-22" placeholder="Enter your password">
				</div>
				<div class="d-flex mt-10 justify-content-between">
					<div class="form-check">
						<input v-model="form.remember_me" class="form-check-input" type="checkbox" id="remember-me">
						<label class="form-check-label" for="remember-me">Remember me</label>
					</div>
					<router-link tag="a" :to="{ name: 'forgot-password' }">Forgot password?</router-link>
				</div>
				<button type="button" class="btn btn-primary btn-block mt-25 py-11" @click="login" :disabled="btnAction != 'Log In' ? true : false">{{ btnAction }}</button>
				<ul v-if="Object.keys(errorMessages).length" class="error-messages pt-20">
					<li v-for="message in errorMessages.username">{{ message }}</li>
					<li v-for="message in errorMessages.password">{{ message }}</li>
				</ul>
				<social-media-login></social-media-login>
			</form>
			<div v-else class="mt-30">
				Your account is not activated yet, please check an activation email in your inbox and click the link in that email. If you did not receive this email <router-link tag="a" :to="{ name: 'resend-activation' }" class="text-primary">click here</router-link> to resend.
			</div>
		</div>
		<div class="bg-white border border-color-1 rounded p-20 rounded text-center fadeInDown animated mt-20">
			Do not have an account? <router-link tag="a" :to="{ name: 'register' }" class="text-primary">Sign up</router-link>
		</div>
		<footer-link></footer-link>
	</div>
</template>

<script>
	import moment from 'moment'
	import auth from '../../api/auth'
	import FooterLink from './FooterLink'
	import SocialMediaLogin from './SocialMediaLogin'

	export default {
		data() {
			return {
				form: {
					client_id: '',
					client_secret: '',
					remember_me: false,
					grant_type: 'password',
					username: '',
					password: '',
					scope: ''
				},
				activation: false,
				btnAction: 'Log In',
				errorMessages: []
			}
		},
		components: {
        	'footer-link': FooterLink,
        	'social-media-login': SocialMediaLogin
        },
		methods: {
			validateLogin() {
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
			getErrorMessages(res) {
				if (res.error == 'invalid_credentials') {
					this.$set(this.errorMessages, 'password', ['Sorry, your password was incorrect. Please double-check your password.'])
				} else {
					this.activation = true
				}
			},
			login() {
				let self = this
				self.btnAction = 'Please wait...'

				if (self.validateLogin()) {
					auth.login(self.form, data => {
						data.login_time = self.form.remember_me ? moment(new Date()) : false
						localStorage.setItem('token', JSON.stringify(data))
						self.$router.push({
							name: 'feed'
						})
					}, err => {
	    				self.btnAction = 'Log In'
						self.form.password = ''
						self.getErrorMessages(err.response.data)
					})
				} else {
					self.btnAction = 'Log In'
				}
			}
		},
		mounted() {
			this.form.client_id = $('meta[name="client-id"]').attr('content')
			this.form.client_secret = $('meta[name="client-secret"]').attr('content')
		}
    }
</script>