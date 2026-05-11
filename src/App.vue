<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<CnAppRoot
		:app-id="appId"
		:manifest="manifest"
		:page-types="pageTypes" />
</template>

<script>
import { CnAppRoot, defaultPageTypes } from '@conduction/nextcloud-vue'
import manifest from './manifest.json'
import IndexPageWrapper from './views/IndexPageWrapper.vue'

// Override the default `index` page type with a wrapper that triggers
// fetch on mount via useListView. See IndexPageWrapper.vue for the
// "why this file exists" comment. The other page types (`detail`,
// `dashboard`, `settings`, ...) keep the library defaults.
const pageTypes = {
	...defaultPageTypes,
	index: IndexPageWrapper,
}

export default {
	name: 'App',
	components: { CnAppRoot },
	data() {
		return {
			appId: 'deskdesk',
			manifest,
			pageTypes,
		}
	},
}
</script>
