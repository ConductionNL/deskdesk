---
sidebar_position: 1
title: Set up floors, zones and desks
description: Model the building once — define floors, group desks into zones, set per-desk metadata (equipment, accessibility).
---

# Set up floors, zones and desks

Before anyone can book a desk, the building has to exist in DeskDesk. The model is straightforward: a *floor* belongs to a building and has *zones*; each zone holds *desks*; each desk carries equipment metadata (monitor count, sit/stand, accessibility flag, locker code).

## Goal

By the end the building is modelled — at least one floor, divided into zones (open-office / quiet / phone-booth / accessible), and each zone populated with desks that carry the metadata users want to filter by.

## Prerequisites

- Admin on the Nextcloud instance.
- OpenRegister installed and the DeskDesk register imported (the configuration import runs on app install).
- A rough floor plan or desk list to work from — the metadata model is flexible enough to capture an existing CSV, but you need to know the desk count per zone.

## Steps

1. Open **Floors** from the in-app navigation. On a fresh install the list is empty.

   ![Floors list view, empty](/screenshots/tutorials/admin/01-floors-zones-01.png)

2. Click **Add Item**. The dialog asks for *Name* (e.g. "Floor 3"), *Building*, *Capacity*, and the *Zones* relation. Each zone has a *Name*, *Kind* (open-office / quiet / phone-booth / accessible), and a *Desk count* preview.

   ![New floor dialog](/screenshots/tutorials/admin/01-floors-zones-02.png)

3. Save the floor. It appears in the Floors list with the zones count visible. Click the floor row to open its detail page, then add desks to each zone — *Display name*, *Equipment* (multi-select: monitor, dual-monitor, sit-stand, dock), *Accessibility flag*, *Locker code* (optional).

   ![Floors list after adding](/screenshots/tutorials/admin/01-floors-zones-03.png)

4. Repeat for every floor in the building. The Floors list summarises building / floor count / total desks — handy for a quick sanity check before opening DeskDesk up to the org.

   ![Floors list — multiple floors](/screenshots/tutorials/admin/01-floors-zones-04.png)

## Verification

The Floors list shows each floor with the right zone count and total desk count. Opening DeskDesk as a regular user surfaces the floor picker populated with these floors; the desks index renders the cards with the equipment metadata.

## Common issues

| Symptom | Fix |
|---|---|
| **Add Item** dialog has no zone fields | The zone schema is not mapped — re-run the configuration import from the [admin settings page](./03-admin-settings.md). |
| Saved a floor but it does not appear in the user's floor picker | Browser cache — hard-reload (Ctrl-Shift-R). Vue Router caches the floor list for ~1 minute. |
| Cannot delete a floor with bookings against its desks | DeskDesk refuses cascading deletes that would orphan bookings. Cancel the bookings first, or move them to a different desk, then delete the floor. |

## Reference

- [Configure the knowledge source](./02-knowledge-source.md) — next admin step.
- [Browse desks](../user/02-browse-desks.md) — the user-facing view of what you just set up.
