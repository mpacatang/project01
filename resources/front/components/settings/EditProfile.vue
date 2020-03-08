<template>
	<form @keyup.enter="updateProfile">
		<div class="form-row">
			<div class="form-group col-md-12">
				<label class="font-weight-bolder" for="name">Name</label>
				<input v-model="form.name" type="text" class="form-control" id="name" placeholder="Name">
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.name">{{ message }}</li>
				</ul>
			</div>
			<div class="form-group col-md-6">
				<label class="font-weight-bolder" for="username">Username</label>
				<input v-model="form.username" type="text" class="form-control" id="username" placeholder="Username">
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.username">{{ message }}</li>
				</ul>
			</div>
			<div class="form-group col-md-6">
				<label class="font-weight-bolder" for="gender">Gender</label>
				<select v-model="form.gender" id="gender" class="form-control">
					<option value="">Not Specified</option>
					<option value="male">Male</option>
					<option value="female">Female</option>
				</select>
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.gender">{{ message }}</li>
				</ul>
			</div>
			<div class="form-group col-md-6">
				<label class="font-weight-bolder" for="phone-number">Phone Number</label>
				<input v-model="form.phone_number" type="text" class="form-control" id="phone-number" placeholder="Phone Number">
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.phone_number">{{ message }}</li>
				</ul>
			</div>
			<div class="form-group col-md-6">
				<label class="font-weight-bolder" for="email">Email</label>
				<input v-model="form.email" type="Email" class="form-control" id="email" placeholder="Email">
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.email">{{ message }}</li>
				</ul>
			</div>
			<div class="form-group col-md-12">
				<label class="font-weight-bolder" for="inputPassword4">Bio</label>
				<textarea v-model="form.bio" class="form-control" placeholder="Bio"></textarea>
				<ul class="error-messages">
					<li class="mt-5" v-for="message in errorMessages.bio">{{ message }}</li>
				</ul>
			</div>
		</div>
		<button type="button" class="btn btn-primary" @click="updateProfile" :disabled="btnAction != 'Submit' ? true : false">{{ btnAction }}</button>
	</form>
</template>

<script>
	import user from '../../api/user'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
			return {
				form: {
					name: '',
					username: '',
					gender: '',
					phone_number: '',
					email: '',
					bio: ''
				},
				btnAction: 'Submit',
				errorMessages: []
			}
		},
    	computed: mapGetters({
            user: 'main/user'
        }),
        watch: {
        	user() {
        		this.initEditProfile()
        	}
        },
        methods: {
        	initEditProfile() {
        		let user = Object.assign({}, this.user)
        		this.form.name = this.user.name
        		this.form.username = this.user.username
        		this.form.gender = this.user.gender
        		this.form.phone_number = this.user.phone_number
        		this.form.email = this.user.email
        		this.form.bio = this.user.bio
        	},
			updateProfile() {
				let self = this
				self.btnAction = 'Please wait...'

				user.updateProfile(self.form, data => {
					self.$store.dispatch('main/setReport', 'Your profile has been updated.')
    				self.btnAction = 'Submit'
				}, err => {
    				self.btnAction = 'Submit'
					self.errorMessages = err.response.data.errors
				})
			}
        },
        mounted() {
        	this.initEditProfile()
        }
    }
</script>