---
sidebar_position: 3
title: Book a desk slot
description: Pick a desk, choose a slot (full day / AM / PM / custom), and confirm the booking.
---

# Book a desk slot

DeskDesk supports four slot types out of the box — *full day*, *AM*, *PM*, and a *custom* range. Bookings hang off a desk and a date; the engine refuses overlaps so two people can never claim the same desk for the same slot.

## Goal

By the end you have booked a desk for a slot, confirmed the booking saved without an overlap warning, and seen it appear in your **Bookings** list.

## Prerequisites

- Floors, zones and desks configured by an admin — see [Set up floors, zones and desks](../admin/01-floors-zones.md).
- DeskDesk open with at least one desk visible in your selected zone.

## Steps

1. Open **Bookings** from the navigation. The list shows your existing bookings (filtered to *My bookings*). On a fresh install it reads *No items found*.

   ![Bookings list, empty](/screenshots/tutorials/user/03-book-a-slot-01.png)

2. Click **Add Item** to open the new-booking dialog. The dialog asks for the *Desk*, the *Date*, the *Slot* (full day / AM / PM / custom), and an optional *Note*. Pick the desk and the slot; the engine pre-fills the date as today.

   ![New booking dialog](/screenshots/tutorials/user/03-book-a-slot-02.png)

3. Save. The booking lands in the Bookings list. If the desk is already booked for the slot, the engine rejects the save and the dialog stays open with a "Desk is already booked for this slot" message — change either the desk, the date, or the slot.

   ![Bookings list with the new entry](/screenshots/tutorials/user/03-book-a-slot-03.png)

4. The Bookings list shows your booking with the desk's display name, the date, the slot label, and a *Cancel* action. Cancelling frees the slot immediately — the desk's status flips back to *Free* on the desks index.

   ![Bookings list — actions](/screenshots/tutorials/user/03-book-a-slot-04.png)

5. Return to the desks index (the home view). The booked desk now shows status *Booked by you* for the relevant slot; other people see it as *Booked* without your name.

   ![Desks index with the booked desk](/screenshots/tutorials/user/03-book-a-slot-05.png)

## Verification

The booking appears in your Bookings list, the booked desk's card on the desks index shows *Booked by you* for that slot, and a second attempt to book the same desk for the same slot is rejected with the overlap message.

## Common issues

| Symptom | Fix |
|---|---|
| Save returns "Desk is already booked for this slot" | Someone else (or you, earlier) took it — change desk, slot or date. The engine is intentionally strict; refunds happen only on explicit cancel. |
| **Desk** dropdown is empty | No desks configured — admin task. |
| Booking saves but does not appear in Calendar | The OpenRegister calendar provider is disabled — admin enables it on the [admin settings page](../admin/03-admin-settings.md). |

## Reference

- [Set up a recurring booking](./04-recurring-booking.md) — once you know you are in every Tuesday.
- [Bookings in Nextcloud Calendar](./05-calendar-sync.md) — what the calendar mirroring looks like.
