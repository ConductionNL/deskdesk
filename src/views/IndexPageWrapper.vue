<!-- SPDX-License-Identifier: EUPL-1.2 -->
<!--
  IndexPageWrapper — a thin wrapper around CnIndexPage that adds the
  auto-fetch the library's default `index` page type doesn't.

  Why this file exists:
    CnPageRenderer's default `index` pageType maps straight to
    CnIndexPage, which is purely presentational. It renders the
    `objects` prop you give it but doesn't fetch anything on mount.
    Manifest-driven apps need *something* to call
    objectStore.fetchCollection() before CnIndexPage renders, and the
    canonical place for that "something" is a useListView() consumer.

    This wrapper does exactly that: it accepts every prop the manifest
    forwards (`register`, `schema`, `columns`, `filters`, `defaultSort`),
    runs useListView() against the configured schema as the objectType,
    triggers refresh() in mounted(), and forwards everything to
    CnIndexPage.

  Registration:
    src/main.js installs this wrapper as the `index` pageType override
    via the cnPageTypes inject map, so every manifest page with
    `type: "index"` renders this wrapper instead of bare CnIndexPage.
    The wrapper is intentionally tiny — once the library wires the
    auto-fetch into the default pageType, this file goes away.
-->
<template>
	<CnIndexPage
		:title="title"
		:description="description"
		:schema="schema"
		:object-type="schema"
		:objects="objects"
		:pagination="pagination"
		:loading="loading"
		:store="objectStore"
		:sort-key="sortKey"
		:sort-order="sortOrder"
		:include-columns="columns"
		:filters="filters"
		@refresh="refresh"
		@sort="onSort"
		@page-changed="onPageChange" />
</template>

<script>
import { CnIndexPage, useListView } from '@conduction/nextcloud-vue'
import { useObjectStore } from '../store/store.js'

export default {
	name: 'IndexPageWrapper',
	components: { CnIndexPage },

	// Props forwarded straight from `page.config` in src/manifest.json.
	// CnPageRenderer spreads page.config onto whichever component the
	// pageType dispatches to.
	props: {
		register: { type: [String, Number], default: '' },
		schema: { type: String, required: true },
		columns: { type: Array, default: () => [] },
		filters: { type: Array, default: () => [] },
		defaultSort: { type: Object, default: () => ({ key: null, order: 'asc' }) },
		title: { type: String, default: '' },
		description: { type: String, default: '' },
	},

	/**
	 * @spec exclude academy tutorial demo — wires useListView() onto the manifest-bridge index wrapper, no spec-worthy behavior
	 */
	setup(props) {
		const objectStore = useObjectStore()
		const listView = useListView(props.schema, {
			objectStore,
			defaultSort: props.defaultSort,
		})

		return { ...listView, objectStore }
	},

	mounted() {
		this.refresh()
	},
}
</script>
