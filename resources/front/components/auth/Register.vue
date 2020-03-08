<template>
	<div class="auth-box">
		<div class="bg-white border border-color-1 rounded p-20 pt-30 rounded text-center fadeInDown animated">
			<div class="logo-box font-weight-bolder text-size-42 lh-42">Yuup</div>
			<form v-if="step == 'register'" class="mt-30">
				<div class="form-input">
					<input type="text" v-model="form.name" class="form-control py-22" placeholder="Full Name">
					<input type="text" v-model="form.username" class="form-control py-22" placeholder="Username">
					<input type="text" v-model="form.email" class="form-control py-22" placeholder="Enter your email">
					<input type="password" v-model="form.password" class="form-control py-22" placeholder="Enter your password">
					<input type="password" v-model="form.password_confirmation" class="form-control py-22" placeholder="Enter your password confirmation">
				</div>
				<div class="form-check text-center mt-17 mx-auto">
					<input v-model="form.tos" class="form-check-input mt-5 mml-15" type="checkbox" id="tos">
					<label class="form-check-label lh-23" for="tos">By signing up, you agree to our <a class="text-primary" href="">Terms</a>, <a class="text-primary" href="">Data Policy</a> and <a class="text-primary" href="">Cookies Policy</a>.</label>
				</div>
				<button type="button" class="btn btn-primary btn-block mt-17 py-11" @click="register" :disabled="btnAction != 'Sign Up' ? true : false">{{ btnAction }}</button>
				<ul v-if="Object.keys(errorMessages).length" class="error-messages pt-20">
					<li v-for="message in errorMessages.name">{{ message }}</li>
					<li v-for="message in errorMessages.username">{{ message }}</li>
					<li v-for="message in errorMessages.email">{{ message }}</li>
					<li v-for="message in errorMessages.password">{{ message }}</li>
					<li v-for="message in errorMessages.password_confirmation">{{ message }}</li>
					<li v-for="message in errorMessages.tos">{{ message }}</li>
				</ul>
				<social-media-login></social-media-login>
			</form>
			<div v-else class="mt-30">
				You're almost done, a verification email was sent to <span class="text-size-15 font-weight-bolder">{{ form.email }}</span>. Open this email and click the link to activate your account.
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
	import SocialMediaLogin from './SocialMediaLogin'

    export default {
        data() {
            return {
                form: {
                	tos: '',
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                step: 'register',
                errorMessages: [],
                btnAction: 'Sign Up'
            }
        },
        components: {
        	'footer-link': FooterLink,
        	'social-media-login': SocialMediaLogin
        },
        methods: {
        	validateRegister() {
				let errorMessages = {}
				if (this.form.name == '') {
					errorMessages['name'] = ['Please fill the name field']
				}

				if (this.form.username == '') {
					errorMessages['username'] = ['Please fill the username field']
				}

				if (this.form.email == '') {
					errorMessages['email'] = ['Please fill the email field']
				}

				if (this.form.password == '') {
					errorMessages['password'] = ['Please fill the password field']
				}

				if (this.form.password_confirmation == '') {
					errorMessages['password_confirmation'] = ['Please fill the password confirmation field']
				}

				if (this.form.tos == 0 || this.form.tos == '') {
					errorMessages['tos'] = ['Please check the terms and conditions field']
				}

				this.errorMessages = errorMessages
				return Object.keys(errorMessages).length ? false : true
			},
            register() {
                let self = this
                self.btnAction = 'Please wait...'

                if (self.validateRegister()) {
	                auth.register(self.form, data => {
	                	self.step = 'activation'
	                    self.btnAction = 'Sign Up'
	                }, err => {
	                    self.errorMessages = err.response.data.errors
	                    self.btnAction = 'Sign Up'
	                })
	            } else {
					self.btnAction = 'Sign Up'
				}
            }
        }
    }
</script>