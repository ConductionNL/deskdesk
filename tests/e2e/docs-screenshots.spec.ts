/*
 * SPDX-FileCopyrightText: 2026 DeskDesk Contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *
 * Documentation screenshot capture suite — deskdesk.
 *
 * This spec is *not* a regression test — it drives the DeskDesk UI
 * through every flow documented under `docs/tutorials/{user,admin}/*.md`
 * and writes a fresh PNG into `docs/static/screenshots/tutorials/<track>/`
 * for each step the markdown references.
 *
 * Run manually whenever the UI changes and tutorial screenshots need
 * to be refreshed:
 *
 *     NEXTCLOUD_URL=http://localhost:8080 \
 *       npx playwright test --project docs-capture
 *
 * Excluded from the default regression run via the `docs-capture`
 * project flag in `playwright.config.ts` so PR pipelines don't
 * reshoot screenshots on every push.
 *
 * Authentication: `playwright.config.ts` wires `globalSetup` (a one-time
 * Nextcloud login → storage state) and `use.storageState`, so the
 * `page` fixture here arrives already signed in.
 *
 * Data dependency: deskdesk stores floors, zones, desks and bookings
 * in OpenRegister. On an instance with no DeskDesk data the list
 * views still render (empty state), the floor picker is empty, and
 * the booking dialog opens with the field shell. The structural
 * screenshots below capture cleanly even then. Flow-detail shots (a
 * populated floor plan, a confirmed booking, a knowledge sidebar
 * with content) need real data; until seed data lands those steps
 * fall back to the relevant empty-state view and the markdown pages
 * that reference uncaptured PNGs warn under
 * `onBrokenMarkdownImages: 'warn'` rather than failing the docs build.
 *
 * Pattern reference: ADR-030 (hydra/openspec/architecture/).
 */

import { test, expect, type Page } from '@playwright/test'
import * as path from 'path'
import * as fs from 'fs'

const SHOT_ROOT = path.resolve(__dirname, '..', '..', 'docs', 'static', 'screenshots', 'tutorials')
const APP = '/apps/deskdesk'

/**
 * Save a viewport screenshot under
 * `docs/static/screenshots/tutorials/<track>/<file>`.
 * Lives under `static/` so Docusaurus copies the PNG into the build
 * root — markdown image refs use `/screenshots/...` (root-absolute).
 */
async function shoot(page: Page, track: 'user' | 'admin', file: string): Promise<void> {
	const dir = path.join(SHOT_ROOT, track)
	if (!fs.existsSync(dir)) {
		fs.mkdirSync(dir, { recursive: true })
	}
	await page.screenshot({ path: path.join(dir, file), fullPage: false, type: 'png' })
}

/**
 * Dismiss anything that overlays the app chrome before we try to click —
 * chiefly Nextcloud's first-run wizard modal, but also any leftover
 * dialog. Best-effort: silently no-op when nothing's there.
 */
async function dismissOverlays(page: Page): Promise<void> {
	const wizard = page.locator('#firstrunwizard')
	if (await wizard.isVisible().catch(() => false)) {
		const close = wizard.getByRole('button', { name: /close|got it|finish|skip/i }).first()
		if (await close.isVisible().catch(() => false)) {
			await close.click().catch(() => {})
		} else {
			await page.keyboard.press('Escape').catch(() => {})
		}
		await wizard.waitFor({ state: 'hidden', timeout: 4000 }).catch(() => {})
	}
	const stray = page.locator('[role="dialog"]:not(#firstrunwizard)')
	if (await stray.first().isVisible().catch(() => false)) {
		await page.keyboard.press('Escape').catch(() => {})
		await page.waitForTimeout(300)
	}
}

/** Navigate to a DeskDesk (or absolute) route and settle. */
async function go(page: Page, route: string): Promise<void> {
	const url = route.startsWith('/apps/') || route.startsWith('/settings/')
		? route
		: `${APP}${route}`
	await page.goto(url).catch(() => { /* tolerate a 404 — caller decides */ })
	await page.waitForLoadState('networkidle').catch(() => { /* idle never fires on some pages */ })
	await dismissOverlays(page)
	await page.waitForTimeout(900)
}

/**
 * Open the create dialog on a list view ("Add Item" / "+") if the button
 * is present, screenshot it, and close it again. Returns whether the
 * dialog appeared.
 */
async function captureCreateDialog(page: Page, track: 'user' | 'admin', file: string): Promise<boolean> {
	const addBtn = page.getByRole('button', { name: /Add Item|Toevoegen|\+ ?Toevoegen|Nieuwe?|Book/i }).first()
	if (!(await addBtn.isVisible().catch(() => false))) {
		return false
	}
	await addBtn.click().catch(() => {})
	const dialog = page.locator('[role="dialog"]:not(#firstrunwizard)').first()
	await dialog.waitFor({ state: 'visible', timeout: 5000 }).catch(() => { /* no dialog */ })
	await page.waitForTimeout(400)
	await shoot(page, track, file)
	const cancel = dialog.getByRole('button', { name: /Cancel|Annuleren/i }).first()
	if (await cancel.isVisible().catch(() => false)) {
		await cancel.click().catch(() => {})
	} else {
		await page.keyboard.press('Escape').catch(() => {})
	}
	await page.waitForTimeout(300)
	return true
}

test.beforeEach(async ({ page }) => {
	page.setViewportSize({ width: 1280, height: 800 })
})

// ---------------------------------------------------------------------------
// USER TRACK — see docs/tutorials/user/
// ---------------------------------------------------------------------------

test.describe('docs: user track', () => {
	test('UN first-launch', async ({ page }) => {
		// docs/tutorials/user/01-first-launch.md
		await go(page, '/')
		await shoot(page, 'user', '01-first-launch-01.png')
		await shoot(page, 'user', '01-first-launch-02.png')
		await shoot(page, 'user', '01-first-launch-03.png')
		await go(page, '/bookings')
		await shoot(page, 'user', '01-first-launch-04.png')
		expect(page.url()).toContain('/apps/deskdesk')
	})

	test('UN browse-desks', async ({ page }) => {
		// docs/tutorials/user/02-browse-desks.md
		await go(page, '/')
		await shoot(page, 'user', '02-browse-desks-01.png')
		await go(page, '/floors')
		await shoot(page, 'user', '02-browse-desks-02.png')
		await shoot(page, 'user', '02-browse-desks-03.png')
		// Try clicking the first row to land on the detail page.
		const firstRow = page.locator('table tbody tr, [role="row"]').nth(1)
		if (await firstRow.isVisible().catch(() => false)) {
			await firstRow.click().catch(() => {})
			await page.waitForLoadState('networkidle').catch(() => {})
			await page.waitForTimeout(700)
		}
		await shoot(page, 'user', '02-browse-desks-04.png')
		await shoot(page, 'user', '02-browse-desks-05.png')
	})

	test('UN book-a-slot', async ({ page }) => {
		// docs/tutorials/user/03-book-a-slot.md
		await go(page, '/bookings')
		await shoot(page, 'user', '03-book-a-slot-01.png')
		const had = await captureCreateDialog(page, 'user', '03-book-a-slot-02.png')
		if (!had) {
			await shoot(page, 'user', '03-book-a-slot-02.png')
		}
		await go(page, '/bookings')
		await shoot(page, 'user', '03-book-a-slot-03.png')
		await shoot(page, 'user', '03-book-a-slot-04.png')
		await go(page, '/')
		await shoot(page, 'user', '03-book-a-slot-05.png')
	})

	test('UN recurring-booking', async ({ page }) => {
		// docs/tutorials/user/04-recurring-booking.md
		await go(page, '/bookings')
		await shoot(page, 'user', '04-recurring-booking-01.png')
		const had = await captureCreateDialog(page, 'user', '04-recurring-booking-02.png')
		if (!had) {
			await shoot(page, 'user', '04-recurring-booking-02.png')
		}
		await go(page, '/bookings')
		await shoot(page, 'user', '04-recurring-booking-03.png')
		await shoot(page, 'user', '04-recurring-booking-04.png')
	})

	test('UN calendar-sync', async ({ page }) => {
		// docs/tutorials/user/05-calendar-sync.md
		await go(page, '/bookings')
		await shoot(page, 'user', '05-calendar-sync-01.png')
		await go(page, '/apps/calendar/')
		await shoot(page, 'user', '05-calendar-sync-02.png')
		await shoot(page, 'user', '05-calendar-sync-03.png')
		await go(page, '/bookings')
		await shoot(page, 'user', '05-calendar-sync-04.png')
	})

	test('UN desk-knowledge', async ({ page }) => {
		// docs/tutorials/user/06-desk-knowledge.md
		await go(page, '/')
		await shoot(page, 'user', '06-desk-knowledge-01.png')
		await go(page, '/floors')
		await shoot(page, 'user', '06-desk-knowledge-02.png')
		// The knowledge sidebar opens from a desk detail page — try the
		// first floor row.
		const firstRow = page.locator('table tbody tr, [role="row"]').nth(1)
		if (await firstRow.isVisible().catch(() => false)) {
			await firstRow.click().catch(() => {})
			await page.waitForLoadState('networkidle').catch(() => {})
			await page.waitForTimeout(700)
		}
		await shoot(page, 'user', '06-desk-knowledge-03.png')
		await shoot(page, 'user', '06-desk-knowledge-04.png')
	})
})

// ---------------------------------------------------------------------------
// ADMIN TRACK — see docs/tutorials/admin/
// ---------------------------------------------------------------------------

test.describe('docs: admin track', () => {
	test('AN floors-zones', async ({ page }) => {
		// docs/tutorials/admin/01-floors-zones.md
		await go(page, '/floors')
		await shoot(page, 'admin', '01-floors-zones-01.png')
		const had = await captureCreateDialog(page, 'admin', '01-floors-zones-02.png')
		if (!had) {
			await shoot(page, 'admin', '01-floors-zones-02.png')
		}
		await go(page, '/floors')
		await shoot(page, 'admin', '01-floors-zones-03.png')
		await shoot(page, 'admin', '01-floors-zones-04.png')
	})

	test('AN knowledge-source', async ({ page }) => {
		// docs/tutorials/admin/02-knowledge-source.md
		await go(page, '/settings/admin/deskdesk')
		await shoot(page, 'admin', '02-knowledge-source-01.png')
		await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight / 3))
		await page.waitForTimeout(300)
		await shoot(page, 'admin', '02-knowledge-source-02.png')
		await page.evaluate(() => window.scrollTo(0, (document.body.scrollHeight * 2) / 3))
		await page.waitForTimeout(300)
		await shoot(page, 'admin', '02-knowledge-source-03.png')
	})

	test('AN admin-settings', async ({ page }) => {
		// docs/tutorials/admin/03-admin-settings.md
		await go(page, '/settings/admin/deskdesk')
		await shoot(page, 'admin', '03-admin-settings-01.png')
		await page.evaluate(() => window.scrollTo(0, 0))
		await page.waitForTimeout(300)
		await shoot(page, 'admin', '03-admin-settings-02.png')
		await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight / 2))
		await page.waitForTimeout(300)
		await shoot(page, 'admin', '03-admin-settings-03.png')
		await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight))
		await page.waitForTimeout(300)
		await shoot(page, 'admin', '03-admin-settings-04.png')
		expect(page.url()).toMatch(/deskdesk/)
	})
})
