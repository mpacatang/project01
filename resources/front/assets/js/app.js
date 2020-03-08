window._ = require('lodash')
window.Popper = require('popper.js').default
window.ClassicEditor = require('@ckeditor/ckeditor5-build-classic')

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery')

    // Vendor
    require('bootstrap')
    require('chart.js')
    require('jquery-slimscroll')
    require('../vendor/select2/dist/js/select2.full.js')
    require('../vendor/bootstrap-daterangepicker/daterangepicker.js')

    // Components
	require('./top-bar')
} catch (e) {}