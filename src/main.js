// SPDX-License-Identifier: EUPL-1.2
import Vue from 'vue'
import { PiniaVuePlugin } from 'pinia'
import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import pinia from './pinia.js'
import router from './router/index.js'
import App from './App.vue'
import { initializeStores } from './store/store.js'

// Library CSS — must be explicit import (webpack tree-shakes side-effect imports from aliased packages)
import '@conduction/nextcloud-vue/css/index.css'

// Global (unscoped) app styles
import './assets/app.css'

Vue.mixin({ methods: { t, n } })
Vue.use(PiniaVuePlugin)

// Create Vue instance to activate Pinia context. We mount AFTER
// initializeStores() so the manifest-driven IndexPageWrapper finds the
// object-store types registered when its mounted() hook fires. Without
// this await, IndexPageWrapper.mounted() races initializeStores() and
// CnIndexPage shows "No items found" on first paint.
const app = new Vue({
	pinia,
	router,
	render: h => h(App),
})

initializeStores().finally(() => {
	app.$mount('#content')
})
