<!-- SPDX-License-Identifier: EUPL-1.2 -->
<!--
  KnowledgeTab — a CnObjectSidebar tab that surfaces knowledge_article
  objects whose `zone` matches the current desk's `zone`.

  Mount path:
    Registered as a custom component in src/App.vue's cnCustomComponents
    map (key: 'knowledge-tab'). The manifest's desks-detail page wires
    it via `config.sidebar.tabs[].component = 'knowledge-tab'`.

  Props (forwarded by CnObjectSidebar -> sharedTabProps):
    - objectId       The current desk's id (uuid)
    - objectType     Always 'desk' on this page

  Data flow:
    1. mounted() fetches the desk from the object store by objectId
    2. Reads desk.zone (e.g. 'east')
    3. Fetches knowledge_article objects filtered by { zone }
    4. Renders title + body + link-back-to-wiki for each article

  The body is rendered as plain text. xWiki ships article bodies as
  xwiki/2.1 syntax; for the tutorial the readability hit is acceptable
  and the "Open in xWiki" link gives the user the rich version. A
  proper xwiki-syntax renderer is a follow-up.
-->
<template>
	<div class="knowledge-tab">
		<NcLoadingIcon v-if="loading" :size="32" />
		<NcEmptyContent v-else-if="!articles.length"
			:name="t('deskdesk', 'No articles for this zone yet')"
			:description="emptyDescription" />
		<article v-for="article in articles" :key="article.id" class="knowledge-tab__article">
			<header class="knowledge-tab__head">
				<h3>{{ article.name }}</h3>
				<a v-if="safeUrl(article.url)"
					:href="safeUrl(article.url)"
					target="_blank"
					rel="noopener noreferrer">
					{{ t('deskdesk', 'Open in wiki') }} ↗
				</a>
			</header>
			<p class="knowledge-tab__body">
				{{ article.body }}
			</p>
		</article>
	</div>
</template>

<script>
import { NcEmptyContent, NcLoadingIcon } from '@nextcloud/vue'
import { useObjectStore } from '../store/store.js'

export default {
	name: 'KnowledgeTab',
	components: { NcEmptyContent, NcLoadingIcon },

	props: {
		objectId: { type: String, required: true },
		objectType: { type: String, default: 'desk' },
	},

	/**
	 * @spec exclude academy tutorial demo — exposes the shared object store to the knowledge tab, no spec-worthy behavior
	 */
	setup() {
		return { objectStore: useObjectStore() }
	},

	data() {
		return { articles: [], loading: true }
	},

	computed: {
		/**
		 * @spec exclude academy tutorial demo — static empty-state copy for the tutorial knowledge tab, no spec-worthy behavior
		 */
		emptyDescription() {
			return this.t('deskdesk', 'Write one in your wiki, tag it with the desk zone, and it will appear here on the next sync.')
		},
	},

	watch: {
		objectId: {
			immediate: true,
			/**
			 * @spec exclude academy tutorial demo — reloads articles when the watched desk id changes, no spec-worthy behavior
			 */
			async handler() {
				await this.load()
			},
		},
	},

	methods: {
		/**
		 * Sanitize a URL to only allow http/https protocols (L1).
		 * Prevents javascript: and data: URIs from being rendered as links.
		 *
		 * @spec exclude academy tutorial demo — URL protocol guard for knowledge article external links
		 * @param {string|null|undefined} url Raw URL from OpenRegister
		 * @return {string|null} Safe URL or null
		 */
		safeUrl(url) {
			if (!url || typeof url !== 'string') return null
			try {
				const parsed = new URL(url)
				if (parsed.protocol === 'https:' || parsed.protocol === 'http:') {
					return url
				}
			} catch {
				// unparseable URL — treat as unsafe
			}
			return null
		},

		/**
		 * @spec exclude academy tutorial demo — fetches zone-scoped knowledge_article objects for the tutorial sidebar tab, no spec-worthy behavior
		 */
		async load() {
			// Capture the target ID at the start so a slower response for a
			// previously-selected desk cannot overwrite the current selection
			// (race-condition guard — issue #59).
			const targetId = this.objectId
			this.loading = true
			try {
				const desk = await this.objectStore.fetchObject(this.objectType, targetId)
				// Discard if the user navigated away while the fetch was in flight.
				if (this.objectId !== targetId) return
				const zone = desk?.zone
				if (!zone) {
					this.articles = []
					return
				}
				// Fetch knowledge_article objects scoped to this zone. The
				// object store's fetchCollection forwards arbitrary query
				// params straight to OpenRegister's filter syntax, so the
				// zone filter lands as ?zone=east.
				await this.objectStore.fetchCollection('knowledge_article', { zone, _limit: 25 })
				// Guard again: a second navigation could have happened while
				// the article fetch was in flight.
				if (this.objectId !== targetId) return
				this.articles = this.objectStore.collections.knowledge_article || []
			} finally {
				// Only clear the loading flag if we still own the slot.
				if (this.objectId === targetId) {
					this.loading = false
				}
			}
		},
	},
}
</script>

<style scoped>
.knowledge-tab {
	padding: 12px 16px;
	display: flex;
	flex-direction: column;
	gap: 16px;
}

.knowledge-tab__article {
	border-bottom: 1px solid var(--color-border);
	padding-bottom: 12px;
}

.knowledge-tab__article:last-child {
	border-bottom: 0;
}

.knowledge-tab__head {
	display: flex;
	align-items: baseline;
	justify-content: space-between;
	gap: 8px;
	margin-bottom: 6px;
}

.knowledge-tab__head h3 {
	margin: 0;
	font-size: 15px;
	font-weight: 600;
}

.knowledge-tab__head a {
	font-size: 12px;
	color: var(--color-primary-element);
	text-decoration: none;
	white-space: nowrap;
}

.knowledge-tab__body {
	margin: 0;
	font-size: 13px;
	line-height: 1.5;
	color: var(--color-text-maxcontrast);
	white-space: pre-wrap;
}
</style>
