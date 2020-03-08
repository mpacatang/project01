<template>
	<div class="modal fade" id="profile-modal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<router-link tag="a" :to="{ name: 'change-password' }" data-dismiss="modal" class="w-100 d-block text-center py-10 border-bottom border-color-1">Change Password</router-link>
						<a @click="logout" class="w-100 d-block text-center py-10 border-bottom border-color-1" href="javascript:;">Logout</a>
						<a class="w-100 d-block text-center py-10" data-dismiss="modal" aria-label="Close" href="javascript:;">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
	    methods: {
    		logout() {
    			$('#profile-modal').modal('hide')
    			localStorage.removeItem('token')
    			this.$store.dispatch('feed/resetPosts')
    			this.$store.dispatch('story/resetStories')
    			this.$router.push({
					name: 'login'
				})
    		}
	    }
    }
</script>