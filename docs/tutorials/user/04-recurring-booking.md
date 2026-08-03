---
sidebar_position: 4
title: Set up a recurring booking
description: Claim a desk for "every Tuesday" — set up a weekly recurrence and let DeskDesk expand it into individual bookings.
---

# Set up a recurring booking

For colleagues with a fixed in-office rhythm — "every Tuesday at my favourite desk" — DeskDesk expands a single recurring booking into one booking per occurrence. The series can be paused, individual occurrences can be cancelled, and the engine still enforces the no-overlap rule per occurrence.

## Goal

By the end you have set up a weekly recurring booking on a single desk, confirmed DeskDesk expanded it into the configured number of occurrences (default 12 weeks), and verified each occurrence shows up in your Bookings list.

## Prerequisites

- A desk you can book — see [Browse desks](./02-browse-desks.md).
- The admin has not disabled recurring bookings on the deployment — see [Admin settings](../admin/03-admin-settings.md) for the toggle.

## Steps

1. Open **Bookings** from the navigation. Click **Add Item** to open the new-booking dialog (same one as [Book a slot](./03-book-a-slot.md)).

   ![Bookings list](/screenshots/tutorials/user/04-recurring-booking-01.png)

2. In the dialog, toggle **Recurring**. The form expands with *Recurrence pattern* (daily / weekly / monthly), *Day of week* (for weekly), *Number of occurrences*, and an end-date picker. Pick "weekly", check the day(s) you want, set the count (e.g. 12 weeks).

   ![Recurring booking dialog](/screenshots/tutorials/user/04-recurring-booking-02.png)

3. Save. DeskDesk creates one parent booking and `N` child occurrences. Any occurrence whose desk is already booked is **dropped with a notice** — the rest are saved. Check your Bookings list for the expanded series.

   ![Bookings list with the recurring series](/screenshots/tutorials/user/04-recurring-booking-03.png)

4. Each occurrence is editable individually. Cancel one and the rest of the series stays intact; cancel the parent and the whole series unwinds. The Bookings list groups recurring occurrences under the parent, with a *Show occurrences* expander.

   ![Recurring series — expanded](/screenshots/tutorials/user/04-recurring-booking-04.png)

## Verification

The Bookings list shows the parent recurring booking with a "Recurring" badge and the configured number of occurrences (minus any that were dropped for overlap). Each individual occurrence appears in Nextcloud Calendar on its date — see [Bookings in Nextcloud Calendar](./05-calendar-sync.md).

## Common issues

| Symptom | Fix |
|---|---|
| **Recurring** toggle is missing | Admin disabled recurring bookings — toggle on the admin settings page. |
| Some occurrences are missing | They were dropped at save time because the desk was already booked. The "save report" dialog lists the dropped dates; pick a different desk or skip those weeks. |
| Cancelling one occurrence unwinds the whole series | You cancelled the parent instead of the occurrence — use the per-row Cancel action, not the parent's. |

## Reference

- [Bookings in Nextcloud Calendar](./05-calendar-sync.md) — every occurrence mirrors into Calendar.
- [Admin settings](../admin/03-admin-settings.md) — where the recurring toggle lives.
