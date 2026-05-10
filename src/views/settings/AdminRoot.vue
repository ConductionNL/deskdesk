<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<div class="deskdesk-admin">
		<CnVersionInfoCard
			:app-name="t('deskdesk', 'App template')"
			:app-version="appVersion"
			:is-up-to-date="true"
			:show-update-button="true"
			:title="t('deskdesk', 'Version information')"
			:description="t('deskdesk', 'Information about the current App template installation')">
			<template #footer>
				<div class="cn-support-info">
					<h4>{{ t('deskdesk', 'Support') }}</h4>
					<p>{{ t('deskdesk', 'For support, contact us at') }} <a href="mailto:support@conduction.nl">support@conduction.nl</a></p>
				</div>
			</template>
		</CnVersionInfoCard>

		<Settings v-if="storesReady" />
	</div>
</template>

<script>
import { CnVersionInfoCard } from '@conduction/nextcloud-vue'
import Settings from './Settings.vue'
import { initializeStores } from '../../store/store.js'

export default {
	name: 'AdminRoot',
	components: {
		CnVersionInfoCard,
		Settings,
	},
	data() {
		return {
			storesReady: false,
			// ADR-004 says "never read app state from DOM". This is a narrow exception:
			// `appVersion` is a one-time boot parameter emitted by the PHP admin template
			// (not user-mutable state), following Nextcloud's idiomatic settings-page
			// bootstrap pattern. For any domain data, fetch via the store / backend API.
			appVersion: document.getElementById('deskdesk-settings')?.dataset?.version || 'Unknown',
		}
	},
	async created() {
		await initializeStores()
		this.storesReady = true
	},
}
</script>

<style scoped>
.deskdesk-admin {
	max-width: 900px;
}
</style>
