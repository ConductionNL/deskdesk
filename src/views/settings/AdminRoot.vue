<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<div class="deskdesk-admin">
		<CnVersionInfoCard
			:app-name="t('deskdesk', 'DeskDesk')"
			:app-version="appVersion"
			:is-up-to-date="true"
			:show-update-button="false"
			:title="t('deskdesk', 'Version information')"
			:description="t('deskdesk', 'Information about the current DeskDesk installation')">
			<template #footer>
				<div class="cn-support-info">
					<h4>{{ t('deskdesk', 'Support') }}</h4>
					<p>{{ t('deskdesk', 'For support, contact us at') }} <a href="mailto:support@conduction.nl">support@conduction.nl</a></p>
				</div>
			</template>
		</CnVersionInfoCard>
	</div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { CnVersionInfoCard } from '@conduction/nextcloud-vue'

export default {
	name: 'AdminRoot',
	components: {
		CnVersionInfoCard,
	},
	data() {
		return {
			appVersion: loadState('deskdesk', 'version', 'Unknown'),
		}
	},
}
</script>

<style scoped>
.deskdesk-admin {
	max-width: 900px;
}
</style>
