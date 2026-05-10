// SPDX-License-Identifier: EUPL-1.2
//
// Vue Router — manifest-driven.
//
// Every entry in manifest.pages becomes a route whose `name` matches
// `page.id` (so CnPageRenderer can dispatch the correct stacked view)
// and whose `path` is `page.route`. The component for every page is
// CnPageRenderer; it reads the matched page from the injected manifest
// and chooses CnIndexPage / CnDetailPage / CnDashboardPage / a custom
// component based on `page.type`.

import Vue from 'vue'
import Router from 'vue-router'
import { generateUrl } from '@nextcloud/router'
import { CnPageRenderer } from '@conduction/nextcloud-vue'
import manifest from '../manifest.json'

Vue.use(Router)

const routes = manifest.pages.map((page) => ({
	name: page.id,
	path: page.route,
	component: CnPageRenderer,
}))

routes.push({ path: '*', redirect: '/' })

export default new Router({
	mode: 'history',
	base: generateUrl('/apps/deskdesk'),
	routes,
})
