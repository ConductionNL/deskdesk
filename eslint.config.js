const {
	defineConfig,
} = require('@eslint/config-helpers')

const js = require('@eslint/js')

const {
	FlatCompat,
} = require('@eslint/eslintrc')

// The `@nextcloud` v8 base is Vue-2 era: on its own it activates ZERO
// `vue/no-deprecated-*` rules, so Vue-2 idioms (`beforeDestroy`, `.sync`,
// `filters:`) survive a green lint. `conductionVue3Fixes` layers the Vue 3
// rules on top and must be spread LAST so it wins. It registers no plugins,
// which is why it layers cleanly onto the @nextcloud base.
//
// CJS: the extensionless subpath works because the package ships no `exports`
// map. From ESM this would need `/eslint/index.js`.
const {
	conductionVue3Fixes,
} = require('@conduction/nextcloud-vue/eslint')

const compat = new FlatCompat({
	baseDirectory: __dirname,
	recommendedConfig: js.configs.recommended,
	allConfig: js.configs.all,
})

module.exports = defineConfig([{
	extends: compat.extends('@nextcloud'),

	settings: {
		'import/resolver': {
			alias: {
				map: [
					['@', './src'],
					['@floating-ui/dom-actual', './node_modules/@floating-ui/dom'],
					['@conduction/nextcloud-vue', '../nextcloud-vue/src'],
				],
				extensions: ['.js', '.ts', '.vue', '.json', '.css'],
			},
		},
	},

	rules: {
		// Allow unused i18n functions (t, n) — imported for future translation wiring
		'no-unused-vars': ['error', { varsIgnorePattern: '^(t|n)$', argsIgnorePattern: '^_' }],
		'jsdoc/require-jsdoc': 'off',
		// @spec is the Conduction OpenSpec traceability tag (gate-16); it is a
		// deliberate, org-wide custom JSDoc tag, not a typo. Same declaration as
		// hermiq's config.
		'jsdoc/check-tag-names': ['warn', { definedTags: ['spec'] }],
		'vue/first-attribute-linebreak': 'off',
		'@typescript-eslint/no-explicit-any': 'off',
		'n/no-missing-import': 'off',
		'import/named': 'off', // disable: trips on exports-map subpaths inside @conduction/nextcloud-vue's nested @nextcloud/vue install
		'import/namespace': 'off', // disable namespace checking to avoid parser requirement
		'import/default': 'off', // disable default import checking to avoid parser requirement
		'import/no-named-as-default': 'off', // disable named-as-default checking to avoid parser requirement
		'import/no-named-as-default-member': 'off', // disable named-as-default-member checking to avoid parser requirement
	},
}, ...conductionVue3Fixes])
