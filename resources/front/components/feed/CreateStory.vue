<template>
	<div class="create-story-modal modal fade" id="create-story-modal" tabindex="-1" role="dialog" aria-labelledby="storyModalLabel" aria-hidden="true">
		<div :class="getStoryClass()" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<template v-if="step == 'preview-photo'">
							<div class="story-image position-relative">
								<div class="close light position-absolute post-12 posl-15" data-dismiss="modal" aria-label="Close"></div>
								<div class="image-box w-100 bg-color-1 position-relative mx-auto rounded">
									<img v-if="photo.length" class="position-absolute pos-0 w-100 h-100 object-fit-cover rounded" ref="photo-thumbnail">
								</div>
								<a @click="setStep('write-caption')" class="add-story d-flex justify-content-center align-items-center font-weight-bold text-white position-absolute posb-20 w-100" href="javascript:;">
									<i class="icon icon-plus text-size-17"></i> <div class="ml-8 text-size-13">Add to your story</div>
								</a>
							</div>
						</template>
						<template v-else>
							<div class="w-100 d-flex px-15 py-12 justify-content-between border-bottom border-color-1">
								<div class="close mt-2" data-dismiss="modal" aria-label="Close"></div>
								<div class="font-weight-bolder text-center">Write Caption</div>
								<a @click="createStory" class="font-weight-bolder text-primary" href="javascript:;">Next</a>
							</div>
							<div class="d-flex p-15">
								<img class="mr-15 fw-30 fh-30 rounded-half-circle" :src="user.photo_url">
								<textarea v-model="caption" class="form-control w-96 rounded-0 border-0 pl-0 mmt-5 resize-none" placeholder="Write a caption..."></textarea>
								<div class="position-relative fw-50 fh-40">
									<img class="position-absolute w-100 h-100 object-fit-cover rounded" ref="photo-thumbnail">
								</div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import story from '../../api/story'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			step: 'preview-photo',
    			caption: ''
    		}
    	},
    	computed: mapGetters({
            user: 'main/user',
            photo: 'createStoryModal/photo'
        }),
        methods: {
        	setStep(step) {
                this.step = step
                this.renderPhoto()
            },
            initModal() {
            	let self = this
            	$('.create-story-modal').on('hidden.bs.modal', function () {
            		self.caption = ''
            		self.step = 'preview-photo'
            		$(self.$refs['photo-thumbnail']).attr('src', '')
            		self.$store.dispatch('createStoryModal/setPhoto', '')
            	})

            	$('.create-story-modal').on('shown.bs.modal', function () {
            		self.renderPhoto()
            	})
            },
            renderPhoto() {
            	let reader = new FileReader()
                let self = this

                reader.onload = function (e) {
                    $(self.$refs['photo-thumbnail']).attr('src', e.target.result)
                }

                reader.readAsDataURL(self.photo[0])
            },
            createStory() {
				let formData = new FormData()
                formData.append('photo', this.photo[0])
                formData.append('caption', this.caption)

                story.createStory(formData, data => {
                    $('.create-story-modal').modal('hide')
                    this.$store.dispatch('main/getProfile')
                    this.$store.dispatch('main/setReport', 'Your story was posted.')
                }, err => {
                    console.log(err.response.data.errors)
                })
			},
			getStoryClass() {
				return {
					'modal-dialog': true,
					'story-preview': this.step == 'preview-photo'
				}
			}
        },
        mounted() {
        	this.initModal()
        }
    }
</script>