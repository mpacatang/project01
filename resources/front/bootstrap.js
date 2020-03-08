window._ = require('lodash');
window.axios = require('axios');
window.Popper = require('popper.js').default
window.$ = window.jQuery = require('jquery')
window.Nanobar = require('nanobar');

require('bootstrap')
require('jquery-slimscroll')

// CSRF Token
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}