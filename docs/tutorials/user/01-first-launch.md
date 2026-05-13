---
sidebar_position: 1
title: Open DeskDesk for the first time
description: Open DeskDesk, find your way around the navigation, and confirm the OpenRegister back end is connected.
---

# Open DeskDesk for the first time

A first look at DeskDesk — where the app lives, what the navigation gives you, and how to tell it is wired up to OpenRegister.

## Goal

By the end you will have opened DeskDesk, recognised the dashboard, the floor plan and the booking list, and confirmed that the OpenRegister-backed lists (Desks, Bookings, Floors) load.

## Prerequisites

- A Nextcloud account on an instance where the **DeskDesk** app is installed and enabled.
- The **OpenRegister** app installed and enabled — DeskDesk stores everything (desks, floors, zones, bookings, knowledge sources) in OpenRegister, so it is a hard dependency.
- At least one floor with desks configured by an admin — see [Set up floors, zones and desks](../admin/01-floors-zones.md). Without that, the floor picker is empty and the booking dialog has nothing to book.

## Steps

1. Open the Nextcloud app menu in the top bar and pick **DeskDesk**. You land on the dashboard / desks index.

   ![DeskDesk dashboard](/screenshots/tutorials/user/01-first-launch-01.png)

2. Read the dashboard tiles — occupancy, *My bookings*, *Popular zones*. On a fresh install they read `0`; they fill in as people book.

   ![Dashboard tiles](/screenshots/tutorials/user/01-first-launch-02.png)

3. The in-app navigation has three top-level entries — **Desks** (the home / floor plan), **Bookings** (your booking history), **Floors** (admin view of the building). Below the divider sit **Settings** and the link to the Nextcloud admin settings page.

   ![DeskDesk navigation](/screenshots/tutorials/user/01-first-launch-03.png)

4. Click **Bookings**. The list view shows every booking on the instance (filtered by *My bookings* by default). An empty install reads *No items found* — expected until someone books.

   ![Bookings list, empty state](/screenshots/tutorials/user/01-first-launch-04.png)

## Verification

You are set up correctly when: the DeskDesk dashboard renders without an error banner, the navigation lists *Desks*, *Bookings*, *Floors*, and clicking **Bookings** shows either rows or a clean *No items found* state — not a load error.

## Common issues

| Symptom | Fix |
|---|---|
| "OpenRegister is not installed or enabled" banner | Install and enable the OpenRegister app, then reload DeskDesk. |
| Floor picker is empty | No floors configured — an admin sets up at least one (see [Floors, zones and desks](../admin/01-floors-zones.md)). |
| DeskDesk is missing from the app menu | The app is not enabled for your account — ask an administrator to enable it (and check it is not restricted to a group you are not in). |

## Reference

- [Browse desks by floor and zone](./02-browse-desks.md) — the next user step.
- [Set up floors, zones and desks](../admin/01-floors-zones.md) — the admin counterpart that creates the data this view reads.
