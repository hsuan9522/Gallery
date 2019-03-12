module.exports = {
	/*
	** Headers of the page
	*/
	head: {
		title: 'gallery',
		meta: [
			{ charset: 'utf-8' },
			{ name: 'viewport', content: 'width=device-width, initial-scale=1' },
			{ hid: 'description', name: 'description', content: 'Nuxt.js project' },
			{ name: 'apple-mobile-web-app-capable', content: 'yes' },
			{ name: 'apple-mobile-web-app-status-bar-style', content: 'default' }
		],
		link: [
			{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
			{ rel: 'apple-touch-icon', href: '/images/apple-touch-icon.png' }
		],
		script: [
			{
				src: 'https://openlayers.org/en/v4.6.5/build/ol.js',
				type: 'text/javascript'
			},
			{
				src: 'https://api.mapbox.com/mapbox.js/plugins/arc.js/v0.1.0/arc.js',
				type: 'text/javascript'
			},
			{
				src: 'https://lib.baomitu.com/velocity/1.2.3/velocity.min.js',
				type: 'text/javascript'
			}
		]
	},
	/*
	** Customize the progress bar color
	*/
	loading: '~/components/loading.vue',

	/*
	** Build configuration
	*/
	build: {
		/*
		** Run ESLint on save
		*/
		extend(config, { isDev, isClient }) {
			if (isDev && isClient) {
				config.module.rules.push({
					enforce: 'pre',
					test: /\.(js|vue)$/,
					loader: 'eslint-loader',
					exclude: /(node_modules)/
				})
			}
		},
		vendor: ['axios'],
	},
	css: [
		// SCSS file in the project
		'@/assets/scss/style.scss',
	],
	plugins: [
		{ src: '~/plugins/font-awesome.js' },
		{ src: '~/plugins/vue-gallery.js', ssr: false },
		{ src: '~/plugins/ga.js', ssr: false }
	],
	modules: [
		'bootstrap-vue/nuxt',

		// Or if you have custom bootstrap CSS...
		[
			'bootstrap-vue/nuxt', {
				css: false
			}
		]
	],
	cssSourceMap: false,

}

