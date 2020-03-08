<template>
	<div class="container mt-100">
		<div class="settings bg-white border border-color-1 d-flex rounded mb-60 mx-auto">
			<div class="settings-menu w-27 pb-10">
				<div class="px-15 pt-15 pb-10 font-weight-bolder">Settings</div>
				<router-link tag="div" :to="{ name: 'edit-profile' }" class="cursor-pointer px-15 py-7" active-class="active" exact>Edit Profile</router-link>
				<router-link tag="div" :to="{ name: 'change-password' }" class="cursor-pointer px-15 py-7" active-class="active" exact>Change Password</router-link>
			</div>
			<div class="w-73 d-flex flex-column p-15">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>