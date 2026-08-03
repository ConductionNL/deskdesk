// SPDX-License-Identifier: EUPL-1.2

// Must stay first: sets __webpack_public_path__ / __webpack_nonce__ before any
// CSS, asset URL or lazy chunk URL is evaluated. See setPublicPath.js.
import './setPublicPath.js'
import { createApp, h } from 'vue'
import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import { registerBuiltinDashboardWidgets } from '@conduction/nextcloud-vue'
import pinia from './pinia.js'
import router from './router/index.js'
import App from './App.vue'
import { initializeStores } from './store/store.js'

// Library CSS — must be explicit import (webpack tree-shakes side-effect imports from aliased packages)
import '@conduction/nextcloud-vue/css/index.css'
// gridstack is an nc-vue peerDependency the library deliberately does NOT bundle,
// stylesheet included. Without it v12 sizes items with an undefined
// `--gs-column-width`, so every dashboard item renders 0 px wide with NO error.
import 'gridstack/dist/gridstack.min.css'

// Global (unscoped) app styles
import './assets/app.css'

// nc-vue marks itself `sideEffects: ["**/*.css"]`, so webpack is free to drop the
// bare imports that register the built-in `stat` / `object-table` dashboard
// widgets — they then render "Widget not available" with no error. An explicit
// call is something webpack cannot tree-shake.
registerBuiltinDashboardWidgets()

// Create the app instance to activate the Pinia context. We mount AFTER
// initializeStores() so the manifest-driven IndexPageWrapper finds the
// object-store types registered when its mounted() hook fires. Without
// this await, IndexPageWrapper.mounted() races initializeStores() and
// CnIndexPage shows "No items found" on first paint.
//
// ⚠️ Mount target is `#deskdesk-app`, NOT `#content`. Vue 2's `$mount()`
// REPLACED the matched element, so mounting on templates/index.php's
// `<div id="content">` quietly replaced Nextcloud's own `#content` wrapper from
// layout.user.php and the duplicate id never showed. Vue 3's `mount()` renders
// INSIDE the match, so the app would end up nested in core's wrapper — and with
// two `#content` elements it is undefined which one is matched. A dedicated host
// id removes the ambiguity entirely.
const app = createApp({
	render: () => h(App),
})

app.mixin({ methods: { t, n } })
app.use(pinia)
app.use(router)

initializeStores().finally(() => {
	app.mount('#deskdesk-app')
})
