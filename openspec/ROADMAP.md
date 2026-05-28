# Roadmap

This document tracks the planned development of DeskDesk.

Features are defined in [`specs/`](specs/). When a feature reaches `planned` status during an `/app-explore` session, it is listed here and an OpenSpec change is created with `/opsx:ff`.

## Status Overview

| Feature | Status | Priority | OpenSpec Change |
|---------|--------|----------|----------------|
| _(no features defined yet — use `/app-explore` to start)_ | — | — | — |

## Phases

### Phase 1 — Foundation

_Desk catalogue (floors, zones, desks), basic booking, and a dashboard. The minimum set that makes the app useful._

### Phase 2 — Enhancement

_Recurring bookings, calendar sync via the OpenRegister calendar provider, and the contextual per-desk knowledge panel (OpenConnector + xWiki)._

### Phase 3 — Polish

_Performance, accessibility improvements, full localization, and hardening for production._

---

## How This Works

1. Run `/app-explore` to define features in `specs/`
2. When a feature is `planned`, add it to the table above
3. Run `/opsx:ff {feature-name}` to create the implementation spec
4. Update the **OpenSpec Change** column with a link to the change directory
5. When all changes for a feature are done, mark the feature `done`
