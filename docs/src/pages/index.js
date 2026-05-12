/**
 * DeskDesk landing page.
 *
 * Composes the brand <DetailHero> + <WidgetShelf> from
 * @conduction/docusaurus-preset/components, mirroring the OpenRegister
 * landing page at openregister.conduction.nl (docs/src/pages/index.js)
 * and the decidesk landing page.
 *
 * Written as .js (not .mdx) because the docs site has the docs plugin
 * pointed at `path: './'`, and an MDX file in src/pages/ trips the
 * MDX-ESM parser even with the docs plugin's `src/**` exclude — likely
 * a quirk of how mdx-loader's micromark stack reuses parser state
 * across files in this Docusaurus 3 + this preset combination.
 * Authoring the page in JSX keeps the same component composition.
 *
 * The preset has no `deskdesk` AppMock variant yet, so the hero's
 * right-hand illustration is an inline token-built mock (a floor plan
 * with desk hexes) rather than `<AppMock app="deskdesk" />`.
 */

import React from 'react';
import Layout from '@theme/Layout';
import {
  DetailHero,
  WidgetShelf,
} from '@conduction/docusaurus-preset/components';

/* Desk glyph, stroke variant — the preset's `.titleIcon svg` rule
   forces `fill: none; stroke: currentColor`, so the hero hex needs a
   line-icon, not the filled `deskdesk/img/app.svg` path. Reads as a
   desk with a small drawer column: the unit you book. */
const DESKDESK_ICON = (
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round">
    <path d="M2.5 7h19" />
    <path d="M4 7v13" />
    <path d="M4 8h9v12" />
    <path d="M16 7v13" />
    <path d="M20 7v13" />
    <path d="M16 11h4" />
    <path d="M16 15h4" />
  </svg>
);

const TAGLINE = (
  <>
    DeskDesk runs flexible desk booking for open-office environments:
    pick a desk on a floor, book a slot, see it appear in your
    Nextcloud Calendar, and pull the booking how-tos straight from the
    company wiki. A reference app for the Conduction stack — registers,
    OpenConnector, xWiki.
  </>
);

/* ── inline token-built app mock (no `deskdesk` AppMock variant) ── */
function DeskFloorMock() {
  // A small floor plan: rows of desk hexes, a few "booked" in cobalt,
  // one highlighted in mint (the slot you just confirmed).
  const desks = [
    'free', 'busy', 'free', 'free', 'busy',
    'free', 'free', 'mine', 'busy', 'free',
    'busy', 'free', 'free', 'free', 'busy',
  ];
  const tone = {
    free: 'var(--c-cobalt-50)',
    busy: 'var(--c-cobalt-300)',
    mine: 'var(--c-mint-500)',
  };
  return (
    <div
      style={{
        display: 'flex',
        flexDirection: 'column',
        gap: 10,
        padding: 16,
        background: 'rgba(255,255,255,0.06)',
        borderRadius: 8,
      }}
    >
      <div
        style={{
          fontFamily: 'var(--conduction-typography-font-family-code)',
          fontSize: 9,
          letterSpacing: '0.08em',
          textTransform: 'uppercase',
          color: 'rgba(255,255,255,0.7)',
        }}
      >
        Floor 3 · North wing
      </div>
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(5, 1fr)',
          gap: 8,
        }}
      >
        {desks.map((state, i) => (
          <span
            key={i}
            style={{
              aspectRatio: '1 / 1.12',
              clipPath: 'var(--hex-pointy-top)',
              background: tone[state],
              border:
                state === 'mine'
                  ? '1px solid var(--c-mint-700, var(--c-mint-500))'
                  : 'none',
            }}
          />
        ))}
      </div>
      <div
        style={{
          display: 'flex',
          alignItems: 'center',
          gap: 12,
          marginTop: 4,
          fontFamily: 'var(--conduction-typography-font-family-code)',
          fontSize: 8,
          letterSpacing: '0.05em',
          textTransform: 'uppercase',
          color: 'rgba(255,255,255,0.65)',
        }}
      >
        <span style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
          <span style={{ width: 9, height: 10, clipPath: 'var(--hex-pointy-top)', background: tone.free }} /> Free
        </span>
        <span style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
          <span style={{ width: 9, height: 10, clipPath: 'var(--hex-pointy-top)', background: tone.busy }} /> Booked
        </span>
        <span style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
          <span style={{ width: 9, height: 10, clipPath: 'var(--hex-pointy-top)', background: tone.mine }} /> Yours
        </span>
      </div>
    </div>
  );
}

/* ── widget panels ── */
function AvailableDesksPanel() {
  const rows = [
    { tone: 'var(--c-cobalt-300)', when: 'all day' },
    { tone: 'var(--c-lavender-300)', when: 'AM' },
    { tone: 'var(--c-mint-500)', when: 'PM' },
    { tone: 'var(--c-forest-300)', when: 'all day' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
      {rows.map((row, i) => (
        <div
          key={i}
          style={{
            display: 'flex',
            alignItems: 'center',
            gap: 8,
            padding: '4px 0',
            borderBottom:
              i < rows.length - 1 ? '1px solid var(--c-cobalt-50)' : 'none',
          }}
        >
          <span
            style={{
              width: 14,
              height: 16,
              clipPath: 'var(--hex-pointy-top)',
              background: row.tone,
              flexShrink: 0,
            }}
          />
          <div
            style={{
              flex: 1,
              display: 'flex',
              flexDirection: 'column',
              gap: 2,
            }}
          >
            <div
              style={{
                height: 4,
                width: '70%',
                background: 'var(--c-cobalt-700)',
                borderRadius: 1,
              }}
            />
            <div
              style={{
                height: 3,
                width: '50%',
                background: 'var(--c-cobalt-200)',
                borderRadius: 1,
              }}
            />
          </div>
          <div
            style={{
              fontFamily: 'var(--conduction-typography-font-family-code)',
              fontSize: 8,
              letterSpacing: '0.05em',
              textTransform: 'uppercase',
              color: 'var(--c-cobalt-500)',
            }}
          >
            {row.when}
          </div>
        </div>
      ))}
    </div>
  );
}

function MyBookingsPanel() {
  const rows = [
    { tone: 'var(--c-mint-500)', label: 'TODAY', w: '85%' },
    { tone: 'var(--c-cobalt-300)', label: 'TOMORROW', w: '70%' },
    { tone: 'var(--c-lavender-300)', label: 'FRI', w: '60%' },
    { tone: 'var(--c-forest-300)', label: 'MON', w: '45%' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
      {rows.map((row, i) => (
        <div
          key={i}
          style={{ display: 'flex', alignItems: 'center', gap: 8 }}
        >
          <span
            style={{
              width: 14,
              height: 16,
              clipPath: 'var(--hex-pointy-top)',
              background: row.tone,
              flexShrink: 0,
            }}
          />
          <div
            style={{
              fontFamily: 'var(--conduction-typography-font-family-code)',
              fontSize: 8,
              fontWeight: 700,
              letterSpacing: '0.05em',
              color: 'var(--c-cobalt-700)',
              minWidth: 56,
            }}
          >
            {row.label}
          </div>
          <div
            style={{
              flex: 1,
              height: 6,
              background: 'var(--c-cobalt-50)',
              borderRadius: 1,
              overflow: 'hidden',
            }}
          >
            <div
              style={{
                height: '100%',
                width: row.w,
                background: 'var(--c-cobalt-300)',
                borderRadius: 1,
              }}
            />
          </div>
        </div>
      ))}
    </div>
  );
}

function PopularZonesPanel() {
  const rows = [
    { tone: 'var(--c-cobalt-300)', pct: '94%' },
    { tone: 'var(--c-lavender-300)', pct: '78%' },
    { tone: 'var(--c-mint-500)', pct: '61%' },
    { tone: 'var(--c-forest-300)', pct: '40%' },
    { tone: 'var(--c-orange-knvb)', pct: '22%' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
      {rows.map((row, i) => (
        <div
          key={i}
          style={{ display: 'flex', alignItems: 'center', gap: 8 }}
        >
          <span
            style={{
              width: 18,
              height: 18,
              borderRadius: 2,
              background: row.tone,
              flexShrink: 0,
            }}
          />
          <div
            style={{
              flex: 1,
              display: 'flex',
              flexDirection: 'column',
              gap: 3,
            }}
          >
            <div
              style={{
                height: 4,
                width: '70%',
                background: 'var(--c-cobalt-700)',
                borderRadius: 1,
              }}
            />
            <div
              style={{
                height: 3,
                width: '50%',
                background: 'var(--c-cobalt-200)',
                borderRadius: 1,
              }}
            />
          </div>
          <div
            style={{
              fontFamily: 'var(--conduction-typography-font-family-code)',
              fontSize: 8,
              letterSpacing: '0.05em',
              textTransform: 'uppercase',
              color: 'var(--c-cobalt-500)',
            }}
          >
            {row.pct}
          </div>
        </div>
      ))}
    </div>
  );
}

const WIDGETS = [
  {
    title: 'Available desks today',
    desc: 'Free desks on the floors you work from, with the slots still open — straight on the dashboard you already open every morning.',
    panel: <AvailableDesksPanel />,
  },
  {
    title: 'My bookings',
    desc: 'Every desk you have reserved, sorted by date, each one already mirrored into your Nextcloud Calendar so nothing slips.',
    panel: <MyBookingsPanel />,
  },
  {
    title: 'Popular zones',
    desc: 'Which zones fill up first this week — so the team can plan office days around where everyone actually sits.',
    panel: <PopularZonesPanel />,
  },
];

export default function Home() {
  return (
    <Layout
      title="DeskDesk"
      description="DeskDesk runs flexible desk booking for open-office environments — pick a desk on a floor, book a slot, sync to Nextcloud Calendar, and surface booking how-tos from the company wiki."
    >
      <main className="marketing-page">
        <DetailHero
          background="cobalt"
          appId="deskdesk"
          status={{ label: 'Beta', color: 'var(--c-orange-knvb)' }}
          version="v0.1"
          locales="EN"
          title="DeskDesk"
          tagline={TAGLINE}
          primaryCta={{
            label: 'Install from app store',
            href: 'https://apps.nextcloud.com/apps/deskdesk',
            tone: 'orange',
          }}
          secondaryCta={{ label: 'Read the docs', href: '/docs/intro' }}
          tertiaryCta={{
            label: 'View on GitHub',
            href: 'https://github.com/ConductionNL/deskdesk',
          }}
          iconColor="var(--c-orange-knvb)"
          icon={DESKDESK_ICON}
          illustration={<DeskFloorMock />}
        />

        <WidgetShelf
          eyebrow="Widgets we ship"
          title="The floor plan follows you to the dashboard."
          lede="Install DeskDesk and the home screen shows the desks free today, the bookings you already hold, and the zones the team is filling first."
          widgets={WIDGETS}
        />
      </main>
    </Layout>
  );
}
