/* eslint-disable camelcase, no-undef */
// SPDX-License-Identifier: EUPL-1.2
/**
 * Webpack runtime bootstrap — MUST be the first import of every entry point.
 *
 * `@nextcloud/webpack-vue-config` hardcodes `output.publicPath` to
 * `/apps/deskdesk/js/`. DeskDesk is installed under a non-default apps path
 * (`apps-extra` / `custom_apps`), so the real webroot differs — and the wrong
 * path does NOT 404: Nextcloud answers 200 with `text/html`, which surfaces as
 * a MIME refusal and `ChunkLoadError` rather than a missing-file error.
 *
 * Vue 2 never exposed this because the old bundle emitted no async chunks. The
 * Vue 3 dependency set (@nextcloud/dialogs@7, @nextcloud/files, @mdi/js) splits
 * into many, and only the routes that touch them break — the entry bundle looks
 * clean. `generateFilePath` resolves the correct path at runtime.
 *
 * This has to run BEFORE the entry's CSS imports: `asset/resource` URLs are
 * computed as `__webpack_require__.p + '<hash>'` when the importing CSS module
 * evaluates, and ES imports evaluate before the entry body's statements — so a
 * dedicated first-imported module is the only ordering that is early enough.
 *
 * `__webpack_nonce__` carries Nextcloud's CSP nonce onto any dynamically
 * injected chunk, which strict CSP would otherwise block.
 */
import { generateFilePath } from '@nextcloud/router'

__webpack_nonce__ = btoa(OC.requestToken)
__webpack_public_path__ = generateFilePath('deskdesk', '', 'js/')
