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
				<a v-if="article.url"
					:href="article.url"
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
		 * @spec exclude academy tutorial demo — fetches zone-scoped knowledge_article objects for the tutorial sidebar tab, no spec-worthy behavior
		 */
		async load() {
			this.loading = true
			try {
				const desk = await this.objectStore.fetchObject(this.objectType, this.objectId)
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
				this.articles = this.objectStore.collections.knowledge_article || []
			} finally {
				this.loading = false
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
