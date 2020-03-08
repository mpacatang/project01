<template>
	<form @keyup.enter="changePassword">
		<div class="form-group">
			<label class="font-weight-bolder" for="old-password">Old Password</label>
			<input v-model="form.old_password" type="password" class="form-control" id="old-password" placeholder="Old Password">
			<ul class="error-messages">
				<li class="mt-5" v-for="message in errorMessages.old_password">{{ message }}</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="font-weight-bolder" for="new-password">New Password</label>
			<input v-model="form.new_password" type="password" class="form-control" id="new-password" placeholder="New Password">
			<ul class="error-messages">
				<li class="mt-5" v-for="message in errorMessages.new_password">{{ message }}</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="font-weight-bolder" for="confirm-new-password">Confirm New Password</label>
			<input v-model="form.new_password_confirmation" type="password" class="form-control" id="confirm-new-password" placeholder="Confirm New Password">
			<ul class="error-messages">
				<li class="mt-5" v-for="message in errorMessages.new_password_confirmation">{{ message }}</li>
			</ul>
		</div>
		<button type="button" class="btn btn-primary" @click="changePassword" :disabled="btnAction != 'Change Password' ? true : false">{{ btnAction }}</button>
	</form>
</template>

<script>
	import auth from '../../api/auth'

    export default {
    	data() {
			return {
				form: {
					old_password: '',
					new_password: '',
					new_password_confirmation: ''
				},
				btnAction: 'Change Password',
				errorMessages: []
			}
		},
        methods: {
			changePassword() {
				let self = this
				self.btnAction = 'Please wait...'

				auth.changePassword(self.form, data => {
					self.$store.dispatch('main/setReport', 'Your password has been changed.')
    				self.btnAction = 'Change Password'
    				self.form.old_password = ''
					self.form.new_password = ''
					self.form.new_password_confirmation = ''
					self.errorMessages = []
				}, err => {
    				self.btnAction = 'Change Password'
					self.errorMessages = err.response.data.errors
				})
			}
        }
    }
</script>