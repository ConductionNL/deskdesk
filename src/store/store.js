// SPDX-License-Identifier: EUPL-1.2
//
// Store initialisation — called once from main.js after Vue mounts.
// Fetches settings, then registers each DeskDesk entity type with the
// shared object store so CnIndexPage / CnDetailPage can fetch them by
// slug. The register id is `deskdesk` (matching <id> in info.xml and
// the x-openregister.app field in lib/Settings/deskdesk_register.json).

import { useObjectStore } from './modules/object.js'
import { useSettingsStore } from './modules/settings.js'

const REGISTER = 'deskdesk'
const SCHEMAS = ['floor', 'desk', 'booking']

export async function initializeStores() {
	const settingsStore = useSettingsStore()
	const objectStore = useObjectStore()

	const config = await settingsStore.fetchSettings()

	const registerId = (config && config.register) || REGISTER

	for (const schema of SCHEMAS) {
		objectStore.registerObjectType(schema, schema, registerId)
	}

	return { settingsStore, objectStore }
}

export { useObjectStore, useSettingsStore }
