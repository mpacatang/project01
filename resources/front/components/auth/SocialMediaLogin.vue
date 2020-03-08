<template>
	<section>
		<!-- <div class="mt-30 mb-20">
			<div class="w-100 fh-1 bg-color-1"></div>
			<div class="fw-90 mx-auto mmt-11 bg-white">or login with</div>
		</div>
		<div class="d-flex justify-content-center">
			<a @click="socialLogin('/social-media-auth/facebook')" href="javascript:;" class="mx-8"><i class="icon mdi text-size-18 mdi-facebook"></i></a>
			<a @click="socialLogin('/social-media-auth/twitter')" href="javascript:;" class="mx-8"><i class="icon mdi text-size-18 mdi-twitter"></i></a>
			<a @click="socialLogin('/social-media-auth/google')" href="javascript:;" class="mx-8"><i class="icon mdi text-size-18 mdi-google"></i></a>
		</div> -->
	</section>
</template>

<script>
	export default {
		methods: {
			socialLogin(platform) {
				window.location.href = platform
			}
		}
    }
</script>