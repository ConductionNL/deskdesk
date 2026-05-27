<!-- SPDX-License-Identifier: EUPL-1.2 -->
<!--
  DetailPageWrapper — bridges manifest config + route params onto
  CnDetailPage. CnPageRenderer spreads `page.config` and
  `$route.params` as raw props onto whatever component renders for
  `type: "detail"`. The route param is `id` (because the route is
  `/desks/:id`) but CnDetailPage's prop is `objectId`. The wrapper
  maps the names and forwards the schema as `objectType` so the
  CnObjectSidebar tabs (and the KnowledgeTab in particular) get the
  ids they need.

  Same shape rationale as IndexPageWrapper: when the library wires
  these renames into the default `detail` pageType, this file goes
  away.
-->
<template>
	<CnDetailPage
		:title="title"
		:description="description"
		:object-id="id"
		:object-type="schema"
		:loading="loading"
		:sidebar="sidebar"
		:sidebar-props="sidebarProps">
		<CnObjectDataWidget
			v-if="object && !loading"
			:object-data="object"
			:object-type="schema"
			:store="objectStore"
			:columns="2"
			:title="title" />
	</CnDetailPage>
</template>

<script>
import { CnDetailPage, CnObjectDataWidget } from '@conduction/nextcloud-vue'
import { useObjectStore } from '../store/store.js'

export default {
	name: 'DetailPageWrapper',
	components: { CnDetailPage, CnObjectDataWidget },

	props: {
		register: { type: [String, Number], default: '' },
		schema: { type: String, required: true },
		sidebar: { type: [Boolean, Object], default: false },
		sidebarProps: { type: Object, default: () => ({}) },
		title: { type: String, default: '' },
		description: { type: String, default: '' },
		// Route params (forwarded by CnPageRenderer via { ...$route.params }).
		id: { type: String, default: '' },
	},

	/**
	 * @spec exclude academy tutorial demo — exposes the shared object store to the manifest-bridge wrapper, no spec-worthy behavior
	 */
	setup() {
		return { objectStore: useObjectStore() }
	},

	data() {
		return { object: null, loading: false }
	},

	watch: {
		id: {
			immediate: true,
			/**
			 * @spec exclude academy tutorial demo — fetches the detail object when the route id changes, no spec-worthy behavior
			 */
			async handler() {
			if (!this.id) return
			// Capture the target ID to guard against races when the user
			// navigates quickly between detail pages (issue #59).
			const targetId = this.id
			this.loading = true
			try {
				const obj = await this.objectStore.fetchObject(this.schema, targetId)
				if (this.id === targetId) {
					this.object = obj
				}
			} finally {
				if (this.id === targetId) {
					this.loading = false
				}
			}
		},
		},
	},
}
</script>
