// SPDX-License-Identifier: EUPL-1.2

// Must stay first: sets __webpack_public_path__ / __webpack_nonce__ — see setPublicPath.js.
import './setPublicPath.js'
import { createApp, h } from 'vue'
import { translate as t, translatePlural as n, loadTranslations } from '@nextcloud/l10n'
import pinia from './pinia.js'
import AdminRoot from './views/settings/AdminRoot.vue'

// The mount used to sit INSIDE the loadTranslations callback. Some Nextcloud
// installs only allow the JS/CSS allowlist through Apache, so
// /custom_apps/deskdesk/l10n/<locale>.json can 404 — and the callback then never
// fires, leaving a blank admin panel with no error. Mount unconditionally and
// let translations arrive (or not) on their own; strings fall back to the
// English source either way.
const app = createApp({
	render: () => h(AdminRoot),
})

app.mixin({ methods: { t, n } })
app.use(pinia)

// Vue 3 renders INSIDE the matched element rather than replacing it, so the
// `#deskdesk-settings` div from templates/settings/admin.php is preserved.
app.mount('#deskdesk-settings')

try {
	const result = loadTranslations('deskdesk', () => {})
	if (result && typeof result.then === 'function') {
		result.then(() => {}, () => {})
	}
} catch {
	// Non-fatal — strings fall back to the English source.
}
