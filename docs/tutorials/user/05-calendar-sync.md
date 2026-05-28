---
sidebar_position: 5
title: See your bookings in Nextcloud Calendar
description: Every booking shows up in Nextcloud Calendar — confirm the sync, recognise the entry shape, and understand what edits propagate.
---

# See your bookings in Nextcloud Calendar

Every DeskDesk booking is mirrored into Nextcloud Calendar via the OpenRegister calendar provider. The mirror is one-way: changes you make on the booking flow into Calendar; changes you make on the Calendar event do not flow back. The point of the sync is visibility — you do not need a second tool to see when you are in the office.

## Goal

By the end you can find a booking you just made in Nextcloud Calendar, recognise the event shape (title, attendees, location, link back to the booking), and understand which fields stay in sync.

## Prerequisites

- Nextcloud Calendar app installed and enabled.
- At least one DeskDesk booking — see [Book a slot](./03-book-a-slot.md).
- The OpenRegister calendar provider toggled on by an admin (default = on).

## Steps

1. Make a booking from DeskDesk — pick a desk, pick a slot, save. Check **Bookings** to confirm it landed.

   ![Bookings list with the new booking](/screenshots/tutorials/user/05-calendar-sync-01.png)

2. Open Nextcloud Calendar. The DeskDesk calendar appears in your sidebar (named "DeskDesk Bookings" by default; colour configurable). Your booking shows up on its date with the slot's time window.

   ![Nextcloud Calendar with DeskDesk entry](/screenshots/tutorials/user/05-calendar-sync-02.png)

3. Open the event. The title is the desk's display name, the location is the floor + zone, and the description links back to the booking in DeskDesk. Attendees are empty by default; recurring occurrences carry the parent series' UID.

   ![Calendar event detail](/screenshots/tutorials/user/05-calendar-sync-03.png)

4. Cancel the booking from DeskDesk. The Calendar entry disappears on the next page reload — sync is event-driven on the OpenRegister side, not a poll, so propagation is near-immediate (under one second).

   ![Bookings list after cancel](/screenshots/tutorials/user/05-calendar-sync-04.png)

## Verification

A new booking from DeskDesk appears in the DeskDesk Bookings calendar in Nextcloud Calendar within a second. Cancelling the booking removes the event the same way. Editing the Calendar event title does **not** rename the DeskDesk booking — the mirror is one-way.

## Common issues

| Symptom | Fix |
|---|---|
| DeskDesk calendar is missing from Calendar's sidebar | The OpenRegister calendar provider is disabled. Admin re-enables it on the [admin settings page](../admin/03-admin-settings.md). |
| Bookings show up but the times are wrong | The slot configuration on the deployment differs from "office time" — admin checks the slot definitions (AM = 09:00–12:30, PM = 13:00–17:30 by default). |
| Edits to the Calendar event reset on reload | Expected — the mirror overwrites Calendar from the DeskDesk side. Edit the booking from DeskDesk. |

## Reference

- [Set up a recurring booking](./04-recurring-booking.md) — every occurrence mirrors as a separate event.
- [Admin settings](../admin/03-admin-settings.md) — slot definitions and the calendar provider toggle.
