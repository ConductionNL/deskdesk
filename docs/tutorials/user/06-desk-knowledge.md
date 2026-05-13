---
sidebar_position: 6
title: Read the how-to articles for a desk
description: Open the contextual knowledge sidebar — the wiki articles for the specific desk you are about to book (wiring, lockers, screens).
---

# Read the how-to articles for a desk

Each desk has a *knowledge sidebar* that pulls articles live from the company wiki — wiring, locker code, screen layout, accessibility notes. The sidebar is fed by an OpenConnector source pointing at xWiki (or any compatible wiki backend), so the same article surfaces in DeskDesk and in the company knowledge base.

## Goal

By the end you can open the knowledge sidebar from a desk's detail page, scan the articles that apply to that specific desk, and use the sidebar to settle questions ("which screen cable does this desk use?") before you sit down.

## Prerequisites

- An admin has wired up at least one *knowledge source* — see [Configure the knowledge source](../admin/02-knowledge-source.md). Without one the sidebar shows *No knowledge source configured*.
- A desk that has articles published against it in the wiki (tagged or named with the desk's identifier).

## Steps

1. From the desks index (the home view), pick a floor and a zone. Hover or tap a desk card.

   ![Desks index](/screenshots/tutorials/user/06-desk-knowledge-01.png)

2. Open **Floors** to navigate the building. Each floor links to its detail page where the desks are laid out by zone.

   ![Floors list](/screenshots/tutorials/user/06-desk-knowledge-02.png)

3. Click into a floor (or directly on a desk card) to open the floor detail. The *Knowledge* tab on the right sidebar shows the articles that match the desk you are looking at.

   ![Floor detail with knowledge sidebar](/screenshots/tutorials/user/06-desk-knowledge-03.png)

4. Click an article in the sidebar. The full article opens in a modal or in a new tab, depending on the deployment's knowledge source configuration. Closing the modal drops you back on the floor view with the desk still highlighted.

   ![Knowledge article — opened](/screenshots/tutorials/user/06-desk-knowledge-04.png)

## Verification

The sidebar lists at least one article for a desk that has wiki content; the article opens in a readable view; closing it returns you to the floor without losing your desk filter.

## Common issues

| Symptom | Fix |
|---|---|
| Sidebar reads "No knowledge source configured" | Admin hasn't wired up the OpenConnector source — see [Configure the knowledge source](../admin/02-knowledge-source.md). |
| Sidebar is empty for a specific desk | The wiki has no article matching that desk's identifier. Either tag the article in xWiki with the desk's ID, or add an article whose title matches. |
| Article opens but is broken / unstyled | The xWiki rendering URL has changed or the OpenConnector mapping is stale — admin checks the OpenConnector source. |

## Reference

- [Configure the knowledge source](../admin/02-knowledge-source.md) — admin task that feeds this sidebar.
- [Browse desks](./02-browse-desks.md) — how to get to the desk where the sidebar lives.
