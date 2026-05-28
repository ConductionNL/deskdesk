<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<CnAppRoot
		:app-id="appId"
		:manifest="manifest"
		:page-types="pageTypes"
		:custom-components="customComponents">
		<template #sidebar>
			<CnObjectSidebar
				v-if="objectSidebarState.active"
				:object-type="objectSidebarState.objectType"
				:object-id="objectSidebarState.objectId"
				:title="objectSidebarState.title"
				:subtitle="objectSidebarState.subtitle"
				:register="objectSidebarState.register"
				:schema="objectSidebarState.schema"
				:tabs="objectSidebarState.tabs"
				:hidden-tabs="objectSidebarState.hiddenTabs"
				:custom-components="customComponents"
				:open.sync="objectSidebarState.open" />
		</template>
	</CnAppRoot>
</template>

<script>
import Vue from 'vue'
import { CnAppRoot, CnObjectSidebar, defaultPageTypes } from '@conduction/nextcloud-vue'
import manifest from './manifest.json'
import IndexPageWrapper from './views/IndexPageWrapper.vue'
import DetailPageWrapper from './views/DetailPageWrapper.vue'
import KnowledgeTab from './views/KnowledgeTab.vue'

// Override the default `index` and `detail` page types with wrappers
// that bridge manifest config + route params onto CnIndexPage and
// CnDetailPage. See {Index,Detail}PageWrapper.vue for the rationale.
// Other page types (`dashboard`, `settings`, ...) keep library defaults.
const pageTypes = {
	...defaultPageTypes,
	index: IndexPageWrapper,
	detail: DetailPageWrapper,
}

// Custom components registry — resolved by name from manifest entries
// like `pages[].config.sidebar.tabs[].component`. The knowledge tab on
// the desks-detail page is wired this way.
const customComponents = {
	'knowledge-tab': KnowledgeTab,
}

export default {
	name: 'App',
	components: { CnAppRoot, CnObjectSidebar },

	/**
	 * CnDetailPage injects `objectSidebarState` and pushes the current
	 * page's sidebar config (objectId, objectType, tabs, ...) into it.
	 * We mount CnObjectSidebar reactively against the same state via
	 * the CnAppRoot `#sidebar` slot.
	 *
	 * @spec exclude academy tutorial demo — Vue provide() glue wiring shared sidebar state, no spec-worthy behavior
	 */
	provide() {
		return {
			objectSidebarState: this.objectSidebarState,
		}
	},

	data() {
		return {
			appId: 'deskdesk',
			manifest,
			pageTypes,
			customComponents,
			objectSidebarState: Vue.observable({
				active: false,
				open: true,
				objectType: '',
				objectId: '',
				title: '',
				subtitle: '',
				register: '',
				schema: '',
				tabs: [],
				hiddenTabs: [],
			}),
		}
	},
}
</script>
