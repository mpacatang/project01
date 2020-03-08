<template>
	<div class="create-post-modal modal fade" id="create-post-modal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content border-0 bg-transparent">
				<div class="modal-body">
					<div class="bg-white rounded">
						<input type="file" class="d-none" ref="post-file" @change="showThumbnail($event.target)">
						<template v-if="step == 'choose-photo'">
							<div class="w-100 d-flex px-15 py-12 justify-content-between border-bottom border-color-1">
								<div class="close mt-2" data-dismiss="modal" aria-label="Close"></div>
								<div class="font-weight-bolder text-center">New Photo Post</div>
								<a @click="setStep('write-caption')" :class="{ 'font-weight-bolder': true, 'text-primary': photo.length, 'text-color-2': !photo.length, 'cursor-no-drop': !photo.length }" href="javascript:;">Next</a>
							</div>
							<div class="p-15">
								<div class="image-box w-100 position-relative mx-auto bg-color-1 rounded">
									<img v-if="photo.length" class="position-absolute pos-0 w-100 h-100 object-fit-cover rounded" ref="photo-thumbnail">
									<div class="position-absolute post-0 w-100 h-100 d-flex">
										<button @click="choosePhoto" :class="{ 'btn': true, 'btn-primary': true, 'font-weight-bolder': true, 'm-auto': !photo.length, 'mb-15': photo.length, 'mr-15': photo.length, 'mt-auto': photo.length, 'ml-auto': photo.length }">
											<i class="icon icon-picture mr-5"></i> {{ !photo.length ? 'Choose Photo' : 'Change Photo' }}
										</button>
									</div>
								</div>
							</div>
						</template>
						<template v-else>
							<div class="w-100 d-flex px-15 py-12 justify-content-between align-items-center border-bottom border-color-1">
								<i @click="setStep('choose-photo')" class="icon text-size-17 icon-arrow-left cursor-pointer"></i>
								<div class="font-weight-bolder text-center">Write Caption</div>
								<a @click="createPost" class="font-weight-bolder text-primary" href="javascript:;">Share</a>
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
	import post from '../../api/post'
	import { mapGetters, mapActions } from 'vuex'

    export default {
    	data() {
    		return {
    			step: 'choose-photo',
    			photo: '',
    			caption: ''
    		}
    	},
    	computed: mapGetters({
            user: 'main/user'
        }),
        methods: {
        	setStep(step) {
        		if (this.photo.length) {
	                this.step = step
	                this.renderPhoto()
	            }
            },
            initModal() {
            	let self = this
            	$('.create-post-modal').on('hidden.bs.modal', function () {
            		$(self.$refs['post-file']).val('')
            		self.step = 'choose-photo'
            		self.caption = ''
            		self.photo = ''
            	})
            },
            choosePhoto() {
	    		$(this.$refs['post-file']).trigger('click')
            },
            showThumbnail(input) {
            	if (input.files && input.files[0]) {
            		this.photo = input.files
            		this.renderPhoto()
                }
            },
            renderPhoto() {
            	let reader = new FileReader()
                let self = this

                reader.onload = function (e) {
                    $(self.$refs['photo-thumbnail']).attr('src', e.target.result)
                }

                reader.readAsDataURL(self.photo[0])
            },
            createPost() {
				let formData = new FormData()
                formData.append('photo', $(this.$refs['post-file'])[0].files[0])
                formData.append('caption', this.caption)

                post.createPost(formData, data => {
                    $('.create-post-modal').modal('hide')
                    this.$store.dispatch('feed/addPost', data)
                    this.$store.dispatch('main/setReport', 'Your photo was posted.')
                }, err => {
                    console.log(err.response.data.errors)
                })
			}
        },
        mounted() {
        	this.initModal()
        }
    }
</script>