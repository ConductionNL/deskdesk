// SPDX-License-Identifier: EUPL-1.2
//
// Store initialisation — called once from main.js after Vue mounts.
// Fetches settings (which now include the numeric register id resolved
// by SettingsService::resolveRegisterIds()), then registers each
// DeskDesk schema with the shared object store so CnIndexPage /
// CnDetailPage can hit /api/objects/{registerId}/{schemaSlug}.

import { useObjectStore } from './modules/object.js'
import { useSettingsStore } from './modules/settings.js'

const SCHEMAS = ['floor', 'desk', 'booking', 'knowledge_article']

export async function initializeStores() {
	const settingsStore = useSettingsStore()
	const objectStore = useObjectStore()

	const config = await settingsStore.fetchSettings()

	// The backend resolves the deskdesk register slug to a numeric id and
	// puts it on `config.registerId` (see SettingsService::getSettings()).
	// When the register hasn't been imported yet the id is null; skip the
	// type registration and let CnAppRoot's dependency-check phase show
	// the empty-state UI prompting an admin to run the import.
	const registerId = config?.registerId
	if (!registerId) {
		return { settingsStore, objectStore }
	}

	for (const schema of SCHEMAS) {
		objectStore.registerObjectType(schema, schema, registerId)
	}

	return { settingsStore, objectStore }
}

export { useObjectStore, useSettingsStore }
