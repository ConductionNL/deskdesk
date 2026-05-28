---
sidebar_position: 2
title: Browse desks by floor and zone
description: Navigate the floor plan, filter by floor and zone, and read each desk's availability at a glance.
---

# Browse desks by floor and zone

DeskDesk models the office as floors → zones → desks. The desks index is the home view; it lets you scan a floor, narrow by zone (open-office / quiet / phone-booth / accessible), and see availability per desk before you book.

## Goal

By the end you can navigate the floor plan, filter by floor and zone, and read a desk's availability status (free / partially booked / fully booked) at a glance.

## Prerequisites

- DeskDesk open — see [Open DeskDesk for the first time](./01-first-launch.md).
- At least one floor configured by an admin, with at least one zone and a few desks — see [Set up floors, zones and desks](../admin/01-floors-zones.md).

## Steps

1. Open **Desks** from the navigation (or click the DeskDesk app icon — it routes to the desks index). You see the floor picker at the top and the desk cards below.

   ![Desks index — floor picker on top](/screenshots/tutorials/user/02-browse-desks-01.png)

2. Open **Floors** to view the configured floors as a list. Each row shows the floor's name, building, number of zones, and total desks.

   ![Floors list](/screenshots/tutorials/user/02-browse-desks-02.png)

3. Each floor row is clickable. The list also surfaces basic metadata (capacity, building) so you can pick the right one without opening every detail page.

   ![Floors list — metadata](/screenshots/tutorials/user/02-browse-desks-03.png)

4. Click a floor to open its detail page. The page shows the floor's zones, the desks within each zone, and each desk's availability for the selected slot.

   ![Floor detail page](/screenshots/tutorials/user/02-browse-desks-04.png)

5. Filter by *zone* — open-office, quiet zone, phone-booth, accessible. Hover or tap a desk card to see its equipment (monitor count, sit-stand, accessibility flag) before you book.

   ![Floor detail with zone filter](/screenshots/tutorials/user/02-browse-desks-05.png)

## Verification

You can pick any configured floor, filter to a zone, and read each desk's *Free* / *Booked* / *Partial* status without booking. The desk card shows the equipment metadata the admin attached on setup.

## Common issues

| Symptom | Fix |
|---|---|
| Floor picker is empty | No floors configured — admin task ([Floors, zones, desks](../admin/01-floors-zones.md)). |
| Zone filter has no entries | The floor exists but the admin did not assign zones. |
| Desk status always reads *Unknown* | The booking schema is mapped but availability queries are returning empty — check that the OpenRegister calendar provider is enabled (see [Admin settings](../admin/03-admin-settings.md)). |

## Reference

- [Book a slot](./03-book-a-slot.md) — once you have picked a desk.
- [Open contextual desk knowledge](./06-desk-knowledge.md) — the wiki sidebar per desk.
