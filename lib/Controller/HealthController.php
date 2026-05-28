<?php

/**
 * DeskDesk Health Controller
 *
 * Public health check endpoint (ADR-006).
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
 * @spec openspec/changes/example-change/tasks.md#task-9
 *   (Illustrative stub per ADR-006 — every app MUST expose `GET /api/health`
 *   returning JSON, publicly accessible. Health check MUST verify OpenRegister
 *   connectivity for apps that depend on it.)
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Controller;

use OCA\DeskDesk\AppInfo\Application;
use OCA\DeskDesk\Service\SettingsService;
use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\AnonRateLimit;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

/**
 * Public health check endpoint (ADR-006).
 *
 * Verifies OpenRegister connectivity. Returns 200 when healthy, 503 otherwise.
 * Public (`#[PublicPage]` + `#[NoCSRFRequired]`) so external probes (Prometheus
 * blackbox exporter, K8s liveness/readiness) can poll without auth.
 */
class HealthController extends Controller
{
    /**
     * Constructor.
     *
     * @param IRequest        $request         The request object
     * @param SettingsService $settingsService For OpenRegister availability check
     * @param IAppManager     $appManager      For reading the app version dynamically
     * @param LoggerInterface $logger          The logger
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-9
     */
    public function __construct(
        IRequest $request,
        private SettingsService $settingsService,
        private IAppManager $appManager,
        private LoggerInterface $logger,
    ) {
        parent::__construct(appName: Application::APP_ID, request: $request);
    }//end __construct()

    /**
     * Health check JSON. Public endpoint.
     *
     * Rate-limited to 60 anonymous requests/minute — sufficient for all
     * reasonable monitoring cadences (ADR-006, issue #60).
     *
     * @return JSONResponse
     *
     * @spec openspec/changes/example-change/tasks.md#task-9
     */
    #[PublicPage]
    #[NoCSRFRequired]
    #[AnonRateLimit(limit: 60, period: 60)]
    public function index(): JSONResponse
    {
        try {
            $openRegister = $this->settingsService->isOpenRegisterAvailable();
            $status       = 'degraded';
            $httpStatus   = Http::STATUS_SERVICE_UNAVAILABLE;
            if ($openRegister === true) {
                $status     = 'ok';
                $httpStatus = Http::STATUS_OK;
            }

            return new JSONResponse(
                [
                    'status'       => $status,
                    'app'          => Application::APP_ID,
                    'version'      => $this->appManager->getAppVersion(Application::APP_ID),
                    'dependencies' => [
                        'openregister' => $openRegister,
                    ],
                ],
                $httpStatus
            );
        } catch (\Throwable $e) {
            $this->logger->error('DeskDesk: health check failed', ['exception' => $e]);
            return new JSONResponse(
                ['status' => 'error', 'message' => 'Health check failed'],
                Http::STATUS_INTERNAL_SERVER_ERROR
            );
        }//end try
    }//end index()
}//end class
