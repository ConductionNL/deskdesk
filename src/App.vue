<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<CnAppRoot
		:app-id="appId"
		:manifest="manifest" />
</template>

<script>
import { CnAppRoot } from '@conduction/nextcloud-vue'
import manifest from './manifest.json'

export default {
	name: 'App',
	components: { CnAppRoot },
	data() {
		return {
			appId: 'deskdesk',
			manifest,
		}
	},
}
</script>
