<?php

/**
 * DeskDesk Settings Service
 *
 * Service for managing DeskDesk application configuration and settings.
 *
 * @category Service
 * @package  OCA\DeskDesk\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2026 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git-id>
 *
 * @link https://conduction.nl
 *
 * @spec openspec/changes/example-change/tasks.md#task-3
 *   (Illustrative file-level @spec tag per ADR-003 — every PHP class must
 *   link back to the OpenSpec change that created or last modified it.)
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Service;

use OCA\DeskDesk\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\IAppConfig;
use OCP\IGroupManager;
use OCP\IUserSession;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for managing DeskDesk application configuration and settings.
 */
class SettingsService
{

    /**
     * Configuration keys managed by this service.
     *
     * @var array<string>
     */
    private const CONFIG_KEYS = [
        'register',
    ];

    /**
     * Stable slug of the register declared by deskdesk_register.json.
     *
     * @var string
     */
    private const REGISTER_SLUG = 'deskdesk';

    /**
     * Schema slugs the frontend consumes. Ordered the way the manifest
     * lists them so the resolved id map matches the menu.
     *
     * @var array<string>
     */
    private const SCHEMA_SLUGS = ['floor', 'desk', 'booking', 'knowledge_article'];

    /**
     * Constructor for the SettingsService.
     *
     * @param IAppConfig         $appConfig    The app config interface
     * @param IAppManager        $appManager   The app manager
     * @param ContainerInterface $container    The container
     * @param IGroupManager      $groupManager The group manager
     * @param IUserSession       $userSession  The user session
     * @param LoggerInterface    $logger       The logger
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function __construct(
        private IAppConfig $appConfig,
        private IAppManager $appManager,
        private ContainerInterface $container,
        private IGroupManager $groupManager,
        private IUserSession $userSession,
        private LoggerInterface $logger,
    ) {
    }//end __construct()

    /**
     * Check whether OpenRegister is installed and available.
     *
     * @return bool
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function isOpenRegisterAvailable(): bool
    {
        return $this->appManager->isInstalled('openregister');
    }//end isOpenRegisterAvailable()

    /**
     * Retrieve all current settings.
     *
     * Returns a flat array containing all app config values plus metadata
     * fields (openregisters, isAdmin) consumed by the frontend.
     *
     * @return array<string,mixed>
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function getSettings(): array
    {
        $settings = [];
        foreach (self::CONFIG_KEYS as $key) {
            $settings[$key] = $this->appConfig->getValueString(Application::APP_ID, $key, '');
        }

        $user    = $this->userSession->getUser();
        $isAdmin = ($user !== null && $this->groupManager->isAdmin($user->getUID()));

        $resolved = $this->resolveRegisterIds();

        $response = array_merge(
            $settings,
            [
                'openregisters' => $this->isOpenRegisterAvailable(),
                'isAdmin'       => $isAdmin,
                'registerId'    => $resolved['registerId'],
                'registerSlug'  => self::REGISTER_SLUG,
            ]
        );

        // schemaIds contains internal OR numeric IDs — expose only to admins to
        // reduce enumeration surface for non-privileged users (issue #55).
        if ($isAdmin === true) {
            $response['schemaIds'] = $resolved['schemaIds'];
        }

        return $response;
    }//end getSettings()

    /**
     * Resolve the register slug + schema slugs to numeric ids.
     *
     * CnIndexPage's object store queries OpenRegister at
     * `/api/objects/{registerId}/{schemaSlug}` and the path segment must
     * be the numeric register id, not the slug. This method resolves
     * both the register and every schema on the way so the frontend can
     * register object types without an extra round-trip.
     *
     * Returns `registerId: null` and an empty `schemaIds` map when
     * OpenRegister isn't installed or the register hasn't been imported
     * yet; the frontend treats `registerId === null` as "show the
     * empty-state, prompt admin to load the configuration".
     *
     * @return array{registerId: int|null, schemaIds: array<string,int>}
     */
    private function resolveRegisterIds(): array
    {
        $empty = ['registerId' => null, 'schemaIds' => []];
        if ($this->isOpenRegisterAvailable() === false) {
            return $empty;
        }

        try {
            $registerMapper = $this->container->get('OCA\OpenRegister\Db\RegisterMapper');
            $register       = $registerMapper->find(self::REGISTER_SLUG);
            $registerId     = (int) $register->getId();
        } catch (\Throwable $e) {
            // Register isn't imported yet; that's expected on first boot
            // before the repair step has run. Log at debug level so the
            // log doesn't fill with noise during fresh installs.
            $this->logger->debug(
                'DeskDesk: register slug not resolved yet',
                ['exception' => $e]
            );
            return $empty;
        }

        $schemaIds = [];
        try {
            $schemaMapper = $this->container->get('OCA\OpenRegister\Db\SchemaMapper');
            foreach (self::SCHEMA_SLUGS as $slug) {
                try {
                    $schemaIds[$slug] = (int) $schemaMapper->find($slug)->getId();
                } catch (\Throwable $e) {
                    // Missing schema is non-fatal; the frontend will skip
                    // any object type whose id is absent.
                    $this->logger->debug(
                        'DeskDesk: schema slug not resolved',
                        ['slug' => $slug, 'exception' => $e]
                    );
                }
            }
        } catch (\Throwable $e) {
            $this->logger->debug(
                'DeskDesk: schema mapper unavailable',
                ['exception' => $e]
            );
        }

        return ['registerId' => $registerId, 'schemaIds' => $schemaIds];
    }//end resolveRegisterIds()

    /**
     * Update settings with the provided data.
     *
     * @param array<string,mixed> $data The data to update
     *
     * @return array<string,mixed> The updated settings
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function updateSettings(array $data): array
    {
        foreach (self::CONFIG_KEYS as $key) {
            if (isset($data[$key]) === true) {
                $this->appConfig->setValueString(Application::APP_ID, $key, (string) $data[$key]);
            }
        }

        return $this->getSettings();
    }//end updateSettings()

    /**
     * Load configuration from deskdesk_register.json via OpenRegister.
     *
     * @param bool $force   Force re-import even if already configured.
     * @param bool $isAdmin Whether the caller is a Nextcloud admin. When true,
     *                      a discriminated `reason` field is added to error responses
     *                      so admins can self-diagnose import failures without
     *                      exposing internal details to non-privileged callers.
     *
     * @return array<string,mixed> Result with success flag, message, version, and
     *                             optionally reason (admin-only on error).
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function loadConfiguration(bool $force=false, bool $isAdmin=false): array
    {
        if ($this->isOpenRegisterAvailable() === false) {
            $this->logger->warning('DeskDesk: OpenRegister not available, skipping register initialization');
            $error = [
                'success' => false,
                'message' => 'OpenRegister is not installed or enabled.',
            ];
            if ($isAdmin === true) {
                $error['reason'] = 'or_missing';
            }

            return $error;
        }

        try {
            // Resolve the bundled register file relative to the Nextcloud root,
            // matching ConfigurationService::importFromFilePath's expectation
            // (path relative to /var/www/html).
            $appPath  = (string) $this->appManager->getAppPath(Application::APP_ID);
            $absolute = $appPath.'/lib/Settings/deskdesk_register.json';
            $ncRoot   = '';
            if (class_exists('OC') === true) {
                $ncRoot = \OC::$SERVERROOT;
            }

            $relative = ltrim($absolute, '/');
            if ($ncRoot !== '' && str_starts_with($absolute, $ncRoot.'/') === true) {
                $relative = substr($absolute, strlen($ncRoot) + 1);
            }

            // M1: fail-closed when the register file is unreadable — do NOT fall
            // through with a hardcoded version string, which would import stale data.
            if (file_exists($absolute) === false) {
                $this->logger->error('DeskDesk: deskdesk_register.json not found at '.$absolute);
                $error = [
                    'success' => false,
                    'message' => 'Configuration import failed.',
                ];
                if ($isAdmin === true) {
                    $error['reason'] = 'file_missing';
                }

                return $error;
            }

            $raw = file_get_contents($absolute);
            if ($raw === false) {
                $this->logger->error('DeskDesk: failed to read deskdesk_register.json at '.$absolute);
                $error = [
                    'success' => false,
                    'message' => 'Configuration import failed.',
                ];
                if ($isAdmin === true) {
                    $error['reason'] = 'file_unreadable';
                }

                return $error;
            }

            $payload = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('DeskDesk: deskdesk_register.json is not valid JSON');
                $error = [
                    'success' => false,
                    'message' => 'Configuration import failed.',
                ];
                if ($isAdmin === true) {
                    $error['reason'] = 'parse_error';
                }

                return $error;
            }

            $version = (string) ($payload['info']['version'] ?? '0.0.0');

            $configurationService = $this->container->get('OCA\OpenRegister\Service\ConfigurationService');
            $result = $configurationService->importFromFilePath(
                appId: Application::APP_ID,
                filePath: $relative,
                version: $version,
                force: $force
            );

            if (empty($result) === false) {
                $this->logger->info('DeskDesk: register configuration imported successfully');
                return [
                    'success' => true,
                    'message' => 'Configuration imported successfully.',
                    'version' => $version,
                ];
            }

            return [
                'success' => false,
                'message' => 'Import returned an empty result.',
            ];
        } catch (\Throwable $e) {
            // ADR-005: log the real error server-side, return a static generic message to clients.
            $this->logger->error(
                'DeskDesk: configuration import failed',
                ['exception' => $e]
            );
            $error = [
                'success' => false,
                'message' => 'Configuration import failed.',
            ];
            if ($isAdmin === true) {
                $error['reason'] = 'or_error';
            }

            return $error;
        }//end try
    }//end loadConfiguration()
}//end class
