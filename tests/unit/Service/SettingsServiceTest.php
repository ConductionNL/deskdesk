<?php

/**
 * Unit tests for SettingsService.
 *
 * Covers the ADR-008 requirement of >= 3 PHPUnit methods per logical unit, and
 * exercises every branch of the service: OpenRegister-availability detection,
 * the isAdmin flag in getSettings(), persistence in updateSettings(), and the
 * three branches of loadConfiguration() (success, OpenRegister-missing, and
 * Throwable-caught per ADR-005).
 *
 * @category Test
 * @package  OCA\DeskDesk\Tests\Unit\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2026 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git-id>
 *
 * @link https://conduction.nl
 *
 * @spec openspec/changes/example-change/tasks.md#task-8
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Tests\Unit\Service;

use OCA\DeskDesk\Service\SettingsService;
use OCP\App\IAppManager;
use OCP\IAppConfig;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Tests for SettingsService.
 */
class SettingsServiceTest extends TestCase
{

    /**
     * The service under test.
     *
     * @var SettingsService
     */
    private SettingsService $service;

    /**
     * Mock IAppConfig.
     *
     * @var IAppConfig&MockObject
     */
    private IAppConfig&MockObject $appConfig;

    /**
     * Mock IAppManager.
     *
     * @var IAppManager&MockObject
     */
    private IAppManager&MockObject $appManager;

    /**
     * Mock ContainerInterface.
     *
     * @var ContainerInterface&MockObject
     */
    private ContainerInterface&MockObject $container;

    /**
     * Mock IGroupManager.
     *
     * @var IGroupManager&MockObject
     */
    private IGroupManager&MockObject $groupManager;

    /**
     * Mock IUserSession.
     *
     * @var IUserSession&MockObject
     */
    private IUserSession&MockObject $userSession;

    /**
     * Mock LoggerInterface.
     *
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface&MockObject $logger;

    /**
     * Set up test fixtures.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->appConfig    = $this->createMock(IAppConfig::class);
        $this->appManager   = $this->createMock(IAppManager::class);
        $this->container    = $this->createMock(ContainerInterface::class);
        $this->groupManager = $this->createMock(IGroupManager::class);
        $this->userSession  = $this->createMock(IUserSession::class);
        $this->logger       = $this->createMock(LoggerInterface::class);

        $this->service = new SettingsService(
            appConfig: $this->appConfig,
            appManager: $this->appManager,
            container: $this->container,
            groupManager: $this->groupManager,
            userSession: $this->userSession,
            logger: $this->logger,
        );

    }//end setUp()

    /**
     * isOpenRegisterAvailable() returns true when OpenRegister is installed.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testIsOpenRegisterAvailableReturnsTrueWhenInstalled(): void
    {
        $this->appManager->expects($this->once())
            ->method('isInstalled')
            ->with('openregister')
            ->willReturn(true);

        self::assertTrue($this->service->isOpenRegisterAvailable());

    }//end testIsOpenRegisterAvailableReturnsTrueWhenInstalled()

    /**
     * isOpenRegisterAvailable() returns false when OpenRegister is not installed.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testIsOpenRegisterAvailableReturnsFalseWhenNotInstalled(): void
    {
        $this->appManager->expects($this->once())
            ->method('isInstalled')
            ->with('openregister')
            ->willReturn(false);

        self::assertFalse($this->service->isOpenRegisterAvailable());

    }//end testIsOpenRegisterAvailableReturnsFalseWhenNotInstalled()

    /**
     * getSettings() returns isAdmin=true when the current user is a group admin.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testGetSettingsReturnsIsAdminTrueForAdminUser(): void
    {
        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn('alice');

        $this->appConfig->expects($this->once())
            ->method('getValueString')
            ->with('deskdesk', 'register', '')
            ->willReturn('some-register-uuid');

        $this->userSession->method('getUser')->willReturn($user);
        $this->groupManager->expects($this->once())
            ->method('isAdmin')
            ->with('alice')
            ->willReturn(true);
        $this->appManager->method('isInstalled')->willReturn(true);

        $result = $this->service->getSettings();

        self::assertSame('some-register-uuid', $result['register']);
        self::assertTrue($result['openregisters']);
        self::assertTrue($result['isAdmin']);

    }//end testGetSettingsReturnsIsAdminTrueForAdminUser()

    /**
     * getSettings() returns isAdmin=false when the current user is not an admin.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testGetSettingsReturnsIsAdminFalseForNonAdminUser(): void
    {
        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn('bob');

        $this->appConfig->method('getValueString')->willReturn('');
        $this->userSession->method('getUser')->willReturn($user);
        $this->groupManager->expects($this->once())
            ->method('isAdmin')
            ->with('bob')
            ->willReturn(false);
        $this->appManager->method('isInstalled')->willReturn(false);

        $result = $this->service->getSettings();

        self::assertFalse($result['isAdmin']);
        self::assertFalse($result['openregisters']);

    }//end testGetSettingsReturnsIsAdminFalseForNonAdminUser()

    /**
     * getSettings() returns isAdmin=false when there is no logged-in user (guest).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testGetSettingsReturnsIsAdminFalseWhenNoUser(): void
    {
        $this->appConfig->method('getValueString')->willReturn('');
        $this->userSession->method('getUser')->willReturn(null);
        // groupManager->isAdmin MUST NOT be called when user is null.
        $this->groupManager->expects($this->never())->method('isAdmin');
        $this->appManager->method('isInstalled')->willReturn(true);

        $result = $this->service->getSettings();

        self::assertFalse($result['isAdmin']);

    }//end testGetSettingsReturnsIsAdminFalseWhenNoUser()

    /**
     * updateSettings() persists every known key via IAppConfig and returns the fresh settings.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testUpdateSettingsPersistsKnownKeysAndReturnsSettings(): void
    {
        $this->appConfig->expects($this->once())
            ->method('setValueString')
            ->with('deskdesk', 'register', 'new-register-uuid');

        // getSettings() re-read after update.
        $this->appConfig->method('getValueString')->willReturn('new-register-uuid');
        $this->userSession->method('getUser')->willReturn(null);
        $this->appManager->method('isInstalled')->willReturn(true);

        $result = $this->service->updateSettings(['register' => 'new-register-uuid']);

        self::assertSame('new-register-uuid', $result['register']);
        self::assertTrue($result['openregisters']);

    }//end testUpdateSettingsPersistsKnownKeysAndReturnsSettings()

    /**
     * updateSettings() ignores unknown keys (only CONFIG_KEYS are written).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testUpdateSettingsIgnoresUnknownKeys(): void
    {
        // 'unknown' is not in CONFIG_KEYS → setValueString MUST NOT be called.
        $this->appConfig->expects($this->never())->method('setValueString');

        $this->appConfig->method('getValueString')->willReturn('');
        $this->userSession->method('getUser')->willReturn(null);
        $this->appManager->method('isInstalled')->willReturn(false);

        $this->service->updateSettings(['unknown' => 'value']);

    }//end testUpdateSettingsIgnoresUnknownKeys()

    /**
     * getSettings() includes schemaIds for admin users (needed to configure
     * the object store) and omits it for non-admin users (reduce enumeration
     * surface — issue #55).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function testGetSettingsIncludesSchemaIdsForAdmin(): void
    {
        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn('alice');

        $this->appConfig->method('getValueString')->willReturn('');
        $this->userSession->method('getUser')->willReturn($user);
        $this->groupManager->method('isAdmin')->with('alice')->willReturn(true);
        $this->appManager->method('isInstalled')->willReturn(false);

        $result = $this->service->getSettings();

        self::assertArrayHasKey('schemaIds', $result, 'Admin must receive schemaIds');

    }//end testGetSettingsIncludesSchemaIdsForAdmin()

    /**
     * getSettings() omits schemaIds for non-admin users.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function testGetSettingsOmitsSchemaIdsForNonAdmin(): void
    {
        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn('bob');

        $this->appConfig->method('getValueString')->willReturn('');
        $this->userSession->method('getUser')->willReturn($user);
        $this->groupManager->method('isAdmin')->with('bob')->willReturn(false);
        $this->appManager->method('isInstalled')->willReturn(false);

        $result = $this->service->getSettings();

        self::assertArrayNotHasKey('schemaIds', $result, 'Non-admin must NOT receive schemaIds');

    }//end testGetSettingsOmitsSchemaIdsForNonAdmin()

    /**
     * loadConfiguration() short-circuits when OpenRegister is missing and
     * logs a warning (per ADR-001: OpenRegister is the data layer, not a hard
     * dep — the app must degrade gracefully).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testLoadConfigurationReturnsFailureWhenOpenRegisterMissing(): void
    {
        $this->appManager->expects($this->once())
            ->method('isInstalled')
            ->with('openregister')
            ->willReturn(false);
        $this->logger->expects($this->once())->method('warning');
        // Container MUST NOT be queried when OpenRegister is absent.
        $this->container->expects($this->never())->method('get');

        $result = $this->service->loadConfiguration();

        self::assertFalse($result['success']);
        self::assertSame('OpenRegister is not installed or enabled.', $result['message']);

    }//end testLoadConfigurationReturnsFailureWhenOpenRegisterMissing()

    /**
     * loadConfiguration() success path — delegates to OpenRegister's
     * ConfigurationService::importFromApp(force: true) and returns its result.
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testLoadConfigurationSuccessPathWithForce(): void
    {
        $this->appManager->method('isInstalled')->willReturn(true);
        // getAppPath() is called to resolve the bundled register file path.
        // The path won't exist in the test FS so production falls back to
        // the default version '0.4.0' (see SettingsService::loadConfiguration).
        $this->appManager->method('getAppPath')->willReturn('/tmp/deskdesk-test-app');

        $configurationService = new class {
            /**
             * Stub importFromFilePath mirroring OpenRegister's ConfigurationService.
             *
             * @param string $appId    The app ID.
             * @param string $filePath The bundle path relative to the NC root.
             * @param string $version  The register version string.
             * @param bool   $force    Whether to force re-import.
             *
             * @return array<string,mixed> A non-empty array on success.
             */
            public function importFromFilePath(string $appId, string $filePath, string $version, bool $force): array
            {
                return ['version' => $version, 'imported' => true];
            }
        };

        $this->container->expects($this->once())
            ->method('get')
            ->with('OCA\OpenRegister\Service\ConfigurationService')
            ->willReturn($configurationService);

        $this->logger->expects($this->once())->method('info');

        $result = $this->service->loadConfiguration(force: true);

        self::assertTrue($result['success']);
        self::assertSame('0.4.0', $result['version']);

    }//end testLoadConfigurationSuccessPathWithForce()

    /**
     * ADR-005 error path — when OpenRegister's ConfigurationService throws,
     * loadConfiguration() MUST log the real exception server-side and return
     * a static generic message (no stack trace, no $e->getMessage() leakage).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-8
     */
    public function testLoadConfigurationCatchesThrowableAndReturnsGenericMessage(): void
    {
        $this->appManager->method('isInstalled')->willReturn(true);
        $this->appManager->method('getAppPath')->willReturn('/tmp/deskdesk-test-app');

        $configurationService = new class {
            /**
             * Throws to exercise the Throwable-caught branch.
             *
             * @param string $appId    The app ID.
             * @param string $filePath The file path.
             * @param string $version  The version.
             * @param bool   $force    Whether to force re-import.
             *
             * @return array<string,mixed>
             */
            public function importFromFilePath(string $appId, string $filePath, string $version, bool $force): array
            {
                throw new \RuntimeException('db exploded — host=secret.internal');
            }
        };

        $this->container->method('get')->willReturn($configurationService);

        // Must log the real error...
        $this->logger->expects($this->once())->method('error');

        $result = $this->service->loadConfiguration();

        // ...but MUST return a static generic message.
        self::assertFalse($result['success']);
        self::assertSame('Configuration import failed.', $result['message']);
        // Ensure the sensitive exception message is NOT leaked.
        self::assertStringNotContainsString('secret', $result['message']);
        self::assertStringNotContainsString('exploded', $result['message']);

    }//end testLoadConfigurationCatchesThrowableAndReturnsGenericMessage()

    /**
     * loadConfiguration() with isAdmin=true adds a discriminated reason field
     * when OpenRegister is missing — helps admins self-diagnose (issue #57).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function testLoadConfigurationAdminReceivesReasonOnOrMissing(): void
    {
        $this->appManager->method('isInstalled')->with('openregister')->willReturn(false);
        $this->logger->expects($this->once())->method('warning');

        $result = $this->service->loadConfiguration(force: false, isAdmin: true);

        self::assertFalse($result['success']);
        self::assertSame('or_missing', $result['reason']);

    }//end testLoadConfigurationAdminReceivesReasonOnOrMissing()

    /**
     * loadConfiguration() without isAdmin does NOT include a reason field —
     * non-admin callers receive only the generic message (ADR-005, issue #57).
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-3
     */
    public function testLoadConfigurationNonAdminDoesNotReceiveReason(): void
    {
        $this->appManager->method('isInstalled')->with('openregister')->willReturn(false);
        $this->logger->method('warning');

        $result = $this->service->loadConfiguration(force: false, isAdmin: false);

        self::assertFalse($result['success']);
        self::assertArrayNotHasKey('reason', $result, 'Non-admin callers must not receive reason field');

    }//end testLoadConfigurationNonAdminDoesNotReceiveReason()
}//end class
