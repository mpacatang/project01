<template>
	<div v-if="report.show" class="report position-fixed posb-0 posx-0 bg-color-12 px-15 py-12 font-weight-bolder text-white d-flex align-items-center">
        {{ report.text }}
	</div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'

    export default {
    	computed: mapGetters({
            report: 'main/report'
        })
    }
</script>