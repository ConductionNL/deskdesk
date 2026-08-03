// SPDX-License-Identifier: EUPL-1.2
const path = require('path')
const fs = require('fs')
const webpack = require('webpack')
const webpackConfig = require('@nextcloud/webpack-vue-config')
const { VueLoaderPlugin } = require('vue-loader')
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin')

const buildMode = process.env.NODE_ENV
const isDev = buildMode === 'development'
webpackConfig.devtool = isDev ? 'cheap-source-map' : 'source-map'

webpackConfig.stats = {
	colors: true,
	modules: false,
}

const appId = 'deskdesk'
webpackConfig.entry = {
	main: {
		import: path.join(__dirname, 'src', 'main.js'),
		filename: appId + '-main.js',
	},
	adminSettings: {
		import: path.join(__dirname, 'src', 'settings.js'),
		filename: appId + '-settings.js',
	},
}

// Use the local library source when explicitly asked for, otherwise the npm
// package.
//
// ⚠️ This used to be opt-OUT (`fs.existsSync(../nextcloud-vue/src)`), and that
// sibling checkout sits on the Vue 2 beta line — so any build run from the
// shared apps-extra checkout silently compiled Vue 2 sources into this app. It
// is now opt-IN via an explicit LOCAL_LIB_PATH, matching hermiq.
const localLib = process.env.LOCAL_LIB_PATH
	? path.resolve(process.env.LOCAL_LIB_PATH)
	: path.resolve(__dirname, '../nextcloud-vue/src')
const useLocalLib = Boolean(process.env.LOCAL_LIB_PATH) && fs.existsSync(localLib)

// Extend the base resolve config (preserves defaults from @nextcloud/webpack-vue-config)
webpackConfig.resolve = webpackConfig.resolve || {}
// NOTE: deliberately NO `resolve.modules = [<app>/node_modules, 'node_modules']`.
// Pinning the app's top-level node_modules first defeats npm's nested
// resolution, so a package that legitimately needs its OWN nested copy of a
// dependency gets the hoisted one instead (@nextcloud/dialogs vs @nextcloud/vue
// disagree on @vueuse/core majors). Standard node resolution lets each consumer
// get the version it was built against.
webpackConfig.resolve.alias = {
	...(webpackConfig.resolve.alias || {}),
	'@': path.resolve(__dirname, 'src'),
	...(useLocalLib ? { '@conduction/nextcloud-vue': localLib } : {}),
	// ⚠️ Alias to the ABSOLUTE FILE, never the package DIRECTORY.
	// @nextcloud/vue@9, @nextcloud/dialogs@7 and vue-router@4 ship an `exports`
	// map with no `main` and no `module`. webpack applies an exports map to
	// *package requests* and never to an already-absolutised path, so the
	// Vue-2-era directory aliases that used to be here resolve to nothing at all.
	//
	// One absolute Vue file so the app and the library share ONE Vue copy — dual
	// copies mean two currentRenderingInstance states and a null crash inside
	// CnAppRoot.
	vue$: path.resolve(__dirname, 'node_modules/vue/dist/vue.runtime.esm-bundler.js'),
	pinia$: path.resolve(__dirname, 'node_modules/pinia'),
	// Dedupe vue-router to ONE copy: @nextcloud/vue@9 pulls its own, and a
	// per-importer resolve gives its RouterLink a different router instance than
	// the one `app.use(router)` installed.
	'vue-router$': path.resolve(__dirname, 'node_modules/vue-router/dist/vue-router.mjs'),
	'@nextcloud/vue$': path.resolve(__dirname, 'node_modules/@nextcloud/vue/dist/index.mjs'),
	'@nextcloud/dialogs$': path.resolve(__dirname, 'node_modules/@nextcloud/dialogs/dist/index.mjs'),
	'@nextcloud/dialogs/style.css$': path.resolve(__dirname, 'node_modules/@nextcloud/dialogs/dist/style.css'),
	// Force the lib's transitive @nextcloud/axios import onto the app's copy;
	// without the exact-match `$` webpack loads a second axios instance and the
	// shared interceptors / CSRF token stop applying.
	'@nextcloud/axios$': path.resolve(__dirname, 'node_modules/@nextcloud/axios'),
}

// Add SCSS rule to the existing module rules
webpackConfig.module.rules.push({
	test: /\.scss$/,
	use: ['style-loader', 'css-loader', 'sass-loader'],
})

// PUBLISHED @conduction/nextcloud-vue dist: rollup emits each SFC as separate
// modules, and the wrapper module's ONLY job is the side-effectful glue
// `script.render = render`. The lib's `sideEffects` allowlist covers `**/*.css`
// and `**/*.vue`, which does NOT glob-match the compiled `*.vue.js` files — so
// webpack tree-shakes the wrapper import away and every Cn component ships
// WITHOUT its render function, rendering as a bare comment node with no warning.
webpackConfig.module.rules.push({
	test: /[\\/]node_modules[\\/]@conduction[\\/]nextcloud-vue[\\/]dist[\\/]/,
	sideEffects: true,
})

// Replace plugins to avoid a duplicate VueLoaderPlugin (the base config
// registers one too).
//
// CRITICAL: re-add the appName / appVersion DefinePlugin entries. The base
// config sets them, and replacing `webpackConfig.plugins` wholesale drops them —
// every @nextcloud/vue mount then logs `The library was used without setting /
// replacing the appName`.
webpackConfig.plugins = [
	new VueLoaderPlugin(),
	new NodePolyfillPlugin({
		additionalAliases: ['process'],
	}),
	new webpack.DefinePlugin({ appName: JSON.stringify(appId) }),
	new webpack.DefinePlugin({ appVersion: JSON.stringify(process.env.npm_package_version) }),
	// Vue 3 esm-bundler feature flags. __VUE_OPTIONS_API__ MUST stay true — both
	// the app and @nextcloud/vue are Options-API based.
	new webpack.DefinePlugin({
		__VUE_OPTIONS_API__: 'true',
		__VUE_PROD_DEVTOOLS__: 'false',
		__VUE_PROD_HYDRATION_MISMATCH_DETAILS__: 'false',
	}),
]

// Code splitting stays off for the app's own modules. The chunk URL problem the
// old comment described is now fixed at the source by src/setPublicPath.js —
// `@nextcloud/webpack-vue-config` hardcodes `/apps/<id>/js/`, and the wrong path
// returns 200 text/html rather than a 404, so it surfaced as ChunkLoadError.
// Chunks the dependency tree creates on its own (dynamic import()) exist either
// way and now resolve correctly; keeping splitChunks off simply avoids having to
// attach extra <script> tags from PHP.
webpackConfig.optimization = {
	...(webpackConfig.optimization || {}),
	splitChunks: false,
}

module.exports = webpackConfig
