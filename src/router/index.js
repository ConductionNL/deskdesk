// SPDX-License-Identifier: EUPL-1.2
//
// Vue Router 4 — manifest-driven.
//
// Every entry in manifest.pages becomes a route whose `name` matches
// `page.id` (so CnPageRenderer can dispatch the correct stacked view)
// and whose `path` is `page.route`. The component for every page is
// CnPageRenderer; it reads the matched page from the injected manifest
// and chooses CnIndexPage / CnDetailPage / CnDashboardPage / a custom
// component based on `page.type`.

import { createRouter, createWebHistory } from 'vue-router'
import { generateUrl } from '@nextcloud/router'
import { CnPageRenderer } from '@conduction/nextcloud-vue'
import manifest from '../manifest.json'

const routes = manifest.pages.map((page) => ({
	name: page.id,
	path: page.route,
	component: CnPageRenderer,
}))

// vue-router 4 REMOVED the bare `path: '*'` wildcard. It does not error — the
// route silently never matches, so the shell renders and `<main>` stays empty.
// The v4 spelling is a named catch-all param.
routes.push({ path: '/:pathMatch(.*)*', redirect: '/' })

export default createRouter({
	history: createWebHistory(generateUrl('/apps/deskdesk')),
	routes,
})
