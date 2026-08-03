<?php

/**
 * DeskDesk Settings Controller
 *
 * Controller for managing DeskDesk application settings.
 *
 * @category Controller
 * @package  OCA\DeskDesk\Controller
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2026 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git-id>
 *
 * @link https://conduction.nl
 *
 * @spec openspec/changes/example-change/tasks.md#task-2
 *   (Illustrative file-level @spec tag per ADR-003 — every PHP class must
 *   link back to the OpenSpec change that created or last modified it.)
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Controller;

use OCA\DeskDesk\AppInfo\Application;
use OCA\DeskDesk\Service\SettingsService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\AuthorizedAdminSetting;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

/**
 * Controller for managing DeskDesk application settings.
 *
 * Demonstrates the ADR-003 Controller → Service pattern: thin controller
 * (routing + validation + response), all business logic delegated to the
 * service. Error responses use static generic messages (ADR-005).
 */
class SettingsController extends Controller
{
    /**
     * Constructor for the SettingsController.
     *
     * @param IRequest        $request         The request object
     * @param SettingsService $settingsService The settings service
     * @param IUserSession    $userSession     The user session
     * @param LoggerInterface $logger          The logger (for server-side error logging)
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-2
     */
    public function __construct(
        IRequest $request,
        private SettingsService $settingsService,
        private IUserSession $userSession,
        private LoggerInterface $logger,
    ) {
        parent::__construct(appName: Application::APP_ID, request: $request);
    }//end __construct()

    /**
     * Retrieve all current settings.
     *
     * Any logged-in user may read settings (register IDs, schema IDs,
     * OpenRegister availability, isAdmin flag) — the data drives the SPA's
     * empty-state and object-store configuration. Not admin-gated because
     * the frontend needs this on initial load before an admin check.
     *
     * @return JSONResponse
     *
     * @spec openspec/changes/example-change/tasks.md#task-2
     */
    #[NoAdminRequired]
    public function index(): JSONResponse
    {
        $user = $this->userSession->getUser();
        if ($user === null) {
            return new JSONResponse(['message' => 'Authentication required'], Http::STATUS_UNAUTHORIZED);
        }

        try {
            return new JSONResponse(
                $this->settingsService->getSettings()
            );
        } catch (\Throwable $e) {
            $this->logger->error(
                'DeskDesk: failed to load settings',
                ['exception' => $e]
            );
            return new JSONResponse(['message' => 'Operation failed'], 500);
        }//end try
    }//end index()

    /**
     * Update settings with provided data.
     *
     * Admin-only — writing app configuration is an admin action per ADR-005.
     * Enforced via AuthorizedAdminSetting middleware attribute.
     *
     * @return JSONResponse
     *
     * @spec openspec/changes/example-change/tasks.md#task-2
     */
    #[AuthorizedAdminSetting(Application::APP_ID)]
    public function create(): JSONResponse
    {
        try {
            $data   = $this->request->getParams();
            $config = $this->settingsService->updateSettings($data);

            return new JSONResponse(
                [
                    'success' => true,
                    'config'  => $config,
                ]
            );
        } catch (\Throwable $e) {
            $this->logger->error(
                'DeskDesk: failed to update settings',
                ['exception' => $e]
            );
            return new JSONResponse(['message' => 'Operation failed'], 500);
        }//end try
    }//end create()

    /**
     * Re-import the configuration from deskdesk_register.json.
     *
     * Forces a fresh import regardless of version, auto-configuring
     * all schema and register IDs from the import result. Admin-only —
     * enforced via AuthorizedAdminSetting middleware attribute.
     *
     * @return JSONResponse
     *
     * @spec openspec/changes/example-change/tasks.md#task-2
     */
    #[AuthorizedAdminSetting(Application::APP_ID)]
    public function load(): JSONResponse
    {
        try {
            // Pass isAdmin: true so loadConfiguration() includes a discriminated
            // `reason` field in error responses to help admins self-diagnose failures
            // (issue #57). Non-admin callers never reach this endpoint.
            $result = $this->settingsService->loadConfiguration(force: true, isAdmin: true);
            return new JSONResponse($result);
        } catch (\Throwable $e) {
            $this->logger->error(
                'DeskDesk: failed to load configuration',
                ['exception' => $e]
            );
            return new JSONResponse(['message' => 'Operation failed'], 500);
        }//end try
    }//end load()
}//end class
