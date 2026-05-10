<!-- SPDX-License-Identifier: EUPL-1.2 -->
<template>
	<div class="deskdesk-dashboard">
		<header class="deskdesk-dashboard__header">
			<h2>{{ t('deskdesk', 'Dashboard') }}</h2>
			<p class="deskdesk-dashboard__lead">
				{{ t('deskdesk', 'Starter overview with sample kpis and activity placeholders. replace this view with your own data.') }}
			</p>
		</header>

		<CnKpiGrid :columns="4">
			<CnStatsBlock
				:title="t('deskdesk', 'Open items')"
				:count="12"
				:count-label="t('deskdesk', 'sample')"
				:icon="FolderOutline"
				variant="primary"
				horizontal />
			<CnStatsBlock
				:title="t('deskdesk', 'Due this week')"
				:count="5"
				:count-label="t('deskdesk', 'sample')"
				:icon="CalendarClock"
				variant="warning"
				horizontal />
			<CnStatsBlock
				:title="t('deskdesk', 'Completed')"
				:count="48"
				:count-label="t('deskdesk', 'sample')"
				:icon="CheckCircleOutline"
				variant="success"
				horizontal />
			<CnStatsBlock
				:title="t('deskdesk', 'Team members')"
				:count="7"
				:count-label="t('deskdesk', 'sample')"
				:icon="AccountGroupOutline"
				variant="default"
				horizontal />
		</CnKpiGrid>

		<div class="deskdesk-dashboard__columns">
			<CnConfigurationCard :title="t('deskdesk', 'Recent activity')">
				<ul class="deskdesk-dashboard__placeholder-list">
					<li>{{ t('deskdesk', 'Placeholder: user opened a record') }}</li>
					<li>{{ t('deskdesk', 'Placeholder: status changed to review') }}</li>
					<li>{{ t('deskdesk', 'Placeholder: comment added') }}</li>
				</ul>
			</CnConfigurationCard>

			<CnConfigurationCard :title="t('deskdesk', 'Quick actions')">
				<p class="deskdesk-dashboard__hint">
					{{ t('deskdesk', 'Wire buttons here to create records, open lists, or deep links. use the sidebar for settings and documentation.') }}
				</p>
			</CnConfigurationCard>
		</div>
	</div>
</template>

<script>
import { CnConfigurationCard, CnKpiGrid, CnStatsBlock } from '@conduction/nextcloud-vue'
import AccountGroupOutline from 'vue-material-design-icons/AccountGroupOutline.vue'
import CalendarClock from 'vue-material-design-icons/CalendarClock.vue'
import CheckCircleOutline from 'vue-material-design-icons/CheckCircleOutline.vue'
import FolderOutline from 'vue-material-design-icons/FolderOutline.vue'

export default {
	name: 'Dashboard',
	components: {
		CnConfigurationCard,
		CnKpiGrid,
		CnStatsBlock,
	},
	data() {
		return {
			FolderOutline,
			CalendarClock,
			CheckCircleOutline,
			AccountGroupOutline,
		}
	},
}
</script>

<style scoped>
.deskdesk-dashboard {
	padding: 8px 4px 24px;
	max-width: 1200px;
}

.deskdesk-dashboard__header {
	margin-bottom: 20px;
}

.deskdesk-dashboard__header h2 {
	margin: 0 0 8px;
	font-size: 22px;
	font-weight: 600;
}

.deskdesk-dashboard__lead {
	margin: 0;
	color: var(--color-text-maxcontrast);
	line-height: 1.5;
}

.deskdesk-dashboard__columns {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 16px;
}

@media (max-width: 900px) {
	.deskdesk-dashboard__columns {
		grid-template-columns: 1fr;
	}
}

.deskdesk-dashboard__placeholder-list {
	margin: 0;
	padding-left: 1.2em;
	line-height: 1.6;
}

.deskdesk-dashboard__hint {
	margin: 0;
	line-height: 1.5;
	color: var(--color-text-maxcontrast);
}
</style>
