<template>
	<section>
		<div class="container mt-65 mt-lg-120 px-0 px-lg-20 pb-50 pb-lg-0">
			<div class="row flex-lg-row-reverse mx-0 mmx-lg-15">
				<div class="col-12 col-lg-4 px-0 p-lg-15">
					<profile></profile>
					<button class="btn btn-primary btn-block d-none d-lg-block font-weight-bolder mt-20" data-toggle="modal" data-target="#create-post-modal">Create Post</button>
					<stories></stories>
					<suggestions></suggestions>
					<footer-links></footer-links>
				</div>
				<div class="col-12 col-lg-8 px-0 p-lg-15">
					<posts></posts>
				</div>
			</div>
		</div>
		<create-post></create-post>
		<create-story></create-story>
	</section>
</template>

<script>
	import Profile from './Profile'
	import Stories from './Stories'
	import Suggestions from './Suggestions'
	import FooterLinks from './FooterLinks'
	import Posts from './Posts'
	import CreatePost from './CreatePost'
	import CreateStory from './CreateStory'

	export default {
		components: {
			'profile': Profile,
			'stories': Stories,
			'suggestions': Suggestions,
			'footer-links': FooterLinks,
			'posts': Posts,
			'create-post': CreatePost,
			'create-story': CreateStory
		}
    }
</script>