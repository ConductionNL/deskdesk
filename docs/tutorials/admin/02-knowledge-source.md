---
sidebar_position: 2
title: Connect the wiki knowledge source
description: Wire up an OpenConnector source pointing at xWiki (or any compatible wiki) so each desk surfaces its how-to articles.
---

# Connect the wiki knowledge source

DeskDesk's *knowledge sidebar* — the per-desk how-to panel — is fed by an OpenConnector *source* pointing at xWiki (or any compatible wiki). The source returns a list of articles plus a render URL; DeskDesk surfaces them under the *Knowledge* tab on the floor detail page.

## Goal

By the end the knowledge sidebar on a desk's detail page shows articles from your wiki, opening one launches the rendered article in a modal or in a new tab, and the same source is reusable across other DeskDesk instances or other Conduction apps.

## Prerequisites

- The **OpenConnector** app installed and enabled.
- An xWiki (or compatible) endpoint reachable from the Nextcloud host — URL, an API user, and authentication credentials (Basic auth, OAuth2, or a personal access token).
- Floors and desks already configured — see [Floors, zones and desks](./01-floors-zones.md).

## Steps

1. Open **Settings → Administration → DeskDesk** from the Nextcloud admin section. Scroll to the *Knowledge* card.

   ![Admin settings - Knowledge card](/screenshots/tutorials/admin/02-knowledge-source-01.png)

2. The card shows the configured *knowledge source* (an OpenConnector source ID + a human label) and a *Test* button. On a fresh install the field is empty.

   ![Admin settings - Knowledge mid](/screenshots/tutorials/admin/02-knowledge-source-02.png)

3. Click **Connect a source**. The dialog opens with two paths: pick an existing OpenConnector source from the dropdown, or create one inline (the create form is the standard OpenConnector source form — endpoint, auth, mapping).

   ![Admin settings - Knowledge bottom](/screenshots/tutorials/admin/02-knowledge-source-03.png)

   For xWiki, the endpoint is usually `https://wiki.example.com/rest/wikis/xwiki/spaces/...`; the mapping flattens xWiki's REST response into the shape DeskDesk expects (`title`, `url`, `summary`, `desk_match`). Use the default mapping the source template ships with — it covers stock xWiki.

4. Save. Click **Test** to confirm DeskDesk can list articles. A successful test returns a sample of articles; a failure surfaces the OpenConnector error inline (most often: auth token wrong, or the endpoint is unreachable from the NC host).

## Verification

The *Test* button returns a list of sample articles. Opening DeskDesk as a regular user, browsing to a desk that has a wiki article tagged with its identifier, and opening the *Knowledge* sidebar shows that article. See [Read the how-to articles for a desk](../user/06-desk-knowledge.md) for the user-facing flow.

## Common issues

| Symptom | Fix |
|---|---|
| Test returns "401 Unauthorized" | The OpenConnector auth credentials are wrong or expired — refresh the token / fix the basic-auth password on the source. |
| Test returns articles but the sidebar stays empty | The article's `desk_match` field does not match the desk's identifier. Either tag the article with the desk ID in xWiki, or adjust the OpenConnector mapping. |
| **Connect a source** dialog is missing | OpenConnector is not installed or enabled — install it from the app store first. |

## Reference

- [Read the how-to articles for a desk](../user/06-desk-knowledge.md) — the user-facing view of this configuration.
- [Admin settings](./03-admin-settings.md) — the wider DeskDesk settings page.
