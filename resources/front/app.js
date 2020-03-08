
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'
import store from './store'
import VueRouter from 'vue-router'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Components
import Layout from './components/layout/Layout'
import Feed from './components/feed/Feed'
import Profile from './components/profile/Profile'
import AuthLayout from './components/auth/AuthLayout'
import Login from './components/auth/Login'
import Register from './components/auth/Register'
import ResendActivation from './components/auth/ResendActivation'
import ForgotPassword from './components/auth/ForgotPassword'
import Suggestions from './components/suggestion/Suggestions'
import Messages from './components/message/Messages'
import MessageDefault from './components/message/MessageDefault'
import MessageDetails from './components/message/MessageDetails'
import Explore from './components/explore/Explore'
import SinglePost from './components/single-post/SinglePost'
import Settings from './components/settings/Settings'
import EditProfile from './components/settings/EditProfile'
import ChangePassword from './components/settings/ChangePassword'
import SocialAuth from './components/auth/SocialAuth'
import Activation from './components/auth/Activation'
import ResetPassword from './components/auth/ResetPassword'

Vue.use(VueRouter)

const routes = [
	{
		path: '/',
		component: Layout,
		children: [
			{
				path: '/',
				name: 'feed',
				component: Feed
			},
			{
				path: 'settings',
				component: Settings,
				children: [
					{
						path: 'edit-profile',
						name: 'edit-profile',
						component: EditProfile
					},
					{
						path: 'change-password',
						name: 'change-password',
						component: ChangePassword
					}
				]
			},
			{
				path: 'suggestions',
				name: 'suggestions',
				component: Suggestions
			},
			{
				path: 'messages',
				component: Messages,
				children: [
					{
						path: '/',
						name: 'messages',
						component: MessageDefault
					},
					{
						path: 'message/:username',
						name: 'message-details',
						component: MessageDetails
					}
				]
			},
			{
				path: 'explore',
				name: 'explore',
				component: Explore
			},
			{
				path: ':username',
				name: 'profile',
				component: Profile
			},
			{
				path: 'p/:slug',
				name: 'single-post',
				component: SinglePost
			}
		]
	},
	{
		path: '/auth',
		component: AuthLayout,
		children: [
			{
				path: 'login',
				name: 'login',
				component: Login
			},
			{
				path: 'register',
				name: 'register',
				component: Register
			},
			{
				path: 'resend-activation',
				name: 'resend-activation',
				component: ResendActivation
			},
			{
				path: 'forgot-password',
				name: 'forgot-password',
				component: ForgotPassword
			},
			{
				path: 'reset-password/:resetCode',
				name: 'reset-password',
				component: ResetPassword
			}
		]
	},
	{
		path: '/social-media-auth/:provider/callback',
		name: 'social-media-auth',
		component: SocialAuth
	},
	{
		path: '/activation/:activationCode',
		name: 'activation',
		component: Activation
	},
	{
	    path: '*',
	    redirect: { 
	    	name: 'login'
	    }
	}
]

const router = new VueRouter({
	base: '/',
	mode: 'history',
	routes: routes,
	scrollBehavior(to, from, savedPosition) {
		if (savedPosition) {
			return savedPosition
		} else {
			return { x: 0, y: 0 }
		}
	}
})

router.beforeEach((to, from, next) => {

	// Nanobar
	const nanobar = new Nanobar();
	nanobar.go(30);
	nanobar.go(76);

	setTimeout(() => {
		nanobar.go(100);
	}, 1000)

	// After login
	let afterLogin = [
		'login',
		'register'
	]

	// Before login
	let beforeLogin = [
		'feed',
		'edit-profile',
		'change-password',
		'suggestions',
		'messages',
		'message-details',
		'explore',
		'profile',
		'single-post'
	]

	if (localStorage.getItem('token') !== null && afterLogin.indexOf(to.name) !== -1) {
		next({ name: 'feed' })
	} else if (localStorage.getItem('token') === null && beforeLogin.indexOf(to.name) !== -1) {
		next({ name: 'login' })
	} else {
		next()
	}
	next()
})

const app = new Vue({
	store,
	router
}).$mount('#main')