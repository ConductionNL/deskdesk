<?php

/**
 * DeskDesk DeepLinkRegistrationListener
 *
 * Registers DeskDesk's deep link URL patterns with OpenRegister's search provider.
 *
 * @category Listener
 * @package  OCA\DeskDesk\Listener
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2026 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git-id>
 *
 * @link https://conduction.nl
 *
 * @spec openspec/changes/example-change/tasks.md#task-4
 *   (Illustrative file-level @spec tag per ADR-003.)
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Listener;

use OCA\OpenRegister\Event\DeepLinkRegistrationEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

/**
 * Registers DeskDesk's deep link URL patterns with OpenRegister's search provider.
 *
 * When a user searches in Nextcloud's unified search, results for DeskDesk schemas
 * will link directly to the relevant detail views in the app.
 *
 * @implements IEventListener<Event>
 */
class DeepLinkRegistrationListener implements IEventListener
{
    /**
     * Handle the deep link registration event.
     *
     * @param Event $event The event to handle
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-4
     */
    public function handle(Event $event): void
    {
        if ($event instanceof DeepLinkRegistrationEvent === false) {
            return;
        }

        // Register Article object deep links (schema.org/Article — see ADR-011).
        // Replace 'deskdesk' with your app ID and update the register slug,
        // schema slug, and URL template to match your app's actual schemas.
        // ADR-004: deep link URL MUST use path format (history mode), NOT hash format.
        $event->register(
            appId: 'deskdesk',
            registerSlug: 'deskdesk',
            schemaSlug: 'article',
            urlTemplate: '/apps/deskdesk/items/{uuid}'
        );

    }//end handle()
}//end class
