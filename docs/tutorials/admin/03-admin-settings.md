---
sidebar_position: 3
title: Manage DeskDesk settings
description: Walk through the DeskDesk admin settings — register import, calendar sync toggle, recurring bookings, slot definitions, knowledge source.
---

# Manage DeskDesk settings

DeskDesk's settings page does five jobs: it confirms the OpenRegister mapping, holds the calendar-sync toggle, holds the recurring-bookings toggle, defines the slot windows (AM / PM / full day), and points at the [knowledge source](./02-knowledge-source.md). All of it lives under **Settings → Administration → DeskDesk** in Nextcloud.

## Goal

By the end every DeskDesk admin setting is set to a sensible value for the deployment, the page reports no errors, and a test booking + a test recurring booking confirm the engine is wired up.

## Prerequisites

- Admin on the Nextcloud instance.
- OpenRegister installed and the DeskDesk register imported (the configuration import runs on app install).

## Steps

1. Open **Settings → Administration → DeskDesk**. The page has five sections — *Registers*, *Calendar sync*, *Recurring bookings*, *Slot definitions*, *Knowledge*. Scroll through to see the layout.

   ![Admin settings - top](/screenshots/tutorials/admin/03-admin-settings-01.png)

2. **Registers** — confirms the OpenRegister mapping for each DeskDesk object type (floor, zone, desk, booking, recurrence, knowledge-source). Each row shows the target register + schema; on a healthy install every row is populated. Click **Re-import configuration** to (re)run the import — useful after a DeskDesk upgrade.

   ![Admin settings - Registers + Calendar](/screenshots/tutorials/admin/03-admin-settings-02.png)

3. **Calendar sync** — toggle controlling whether DeskDesk publishes each booking into Nextcloud Calendar via the OpenRegister calendar provider. Default = on. **Recurring bookings** — toggle to allow or disallow recurring bookings org-wide (some deployments forbid them to keep desk allocation fair). Default = on.

   ![Admin settings - Recurring + Slots](/screenshots/tutorials/admin/03-admin-settings-03.png)

4. **Slot definitions** — defines the time windows for *AM*, *PM*, *Full day*, and any *Custom* slots. Edit the defaults to match your office hours (the shipped defaults are AM 09:00–12:30, PM 13:00–17:30, Full day 09:00–17:30). **Knowledge** — the source for the per-desk how-to sidebar, set up in the [knowledge-source tutorial](./02-knowledge-source.md).

   ![Admin settings - Knowledge + bottom](/screenshots/tutorials/admin/03-admin-settings-04.png)

## Verification

The Registers section shows every row populated. Toggling Calendar sync off and re-saving prevents new bookings from appearing in Calendar (existing entries stay). Adjusting a slot window and creating a fresh booking puts the new times on the booking. The page itself reports no error banner.

## Common issues

| Symptom | Fix |
|---|---|
| Registers row stuck unmapped after re-import | The import is failing server-side — check `nextcloud.log` for the *deskdesk* configuration error. Bring DeskDesk + OpenRegister to compatible versions and retry. |
| Calendar sync toggle does nothing visible | The OpenRegister calendar provider is missing or disabled. Check OpenRegister's settings and re-enable. |
| Slot definitions don't show on a new booking | Browser cache — hard-reload (Ctrl-Shift-R). The slot list is cached in Vue's pinia store. |
| **Knowledge** card has no *Connect a source* button | OpenConnector is not installed — install it before continuing. |

## Reference

- [Set up floors, zones and desks](./01-floors-zones.md) — the first admin step.
- [Connect the wiki knowledge source](./02-knowledge-source.md) — the *Knowledge* section's deep-dive.
- [Open DeskDesk for the first time](../user/01-first-launch.md) — the user-facing check after this is done.
