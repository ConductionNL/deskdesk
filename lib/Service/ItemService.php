<?php

/**
 * DeskDesk Item Service
 *
 * Service for domain-level operations on Booking objects, including the ADR-005
 * per-object authorization check demonstrated on delete().
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
 * @spec openspec/changes/example-change/tasks.md#task-5
 */

declare(strict_types=1);

namespace OCA\DeskDesk\Service;

use OCA\DeskDesk\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\IAppConfig;
use OCP\IGroupManager;
use Psr\Container\ContainerInterface;

/**
 * Service for Booking (item) operations.
 *
 * ## ADR-005 per-object authorization
 *
 * `delete()` demonstrates the `#[NoAdminRequired]` + per-object auth pattern
 * documented in ADR-005 and OWASP A01 (Broken Access Control):
 *
 * 1. The controller declares `#[NoAdminRequired]` so non-admins can reach it.
 * 2. This service performs an **explicit backend check** that the caller is
 *    either (a) a Nextcloud group admin, or (b) the creator of the booking
 *    (`@self.owner`) or the booked-for user (`booking.user`).
 * 3. On failure the service returns a `Status` enum value the controller maps
 *    to the right HTTP code (403/404/204) — controllers are thin, services
 *    own the rules.
 *
 * The auth check MUST run on the backend, not the frontend, because the
 * frontend is untrusted (ADR-005). Returning 403 vs 404 also matters for
 * OWASP A01: a plain 403 on non-existent IDs leaks existence, so we return
 * 404 for "not found" and 403 only when the object exists but the caller
 * cannot touch it.
 */
class ItemService
{

    /**
     * Result status enum used by delete() so the controller can map to HTTP codes.
     */
    public const STATUS_DELETED = 'deleted';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_FORBIDDEN = 'forbidden';

    public const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * The schema slug for bookings.
     *
     * @var string
     */
    private const SCHEMA_BOOKING = 'booking';

    /**
     * Constructor for ItemService.
     *
     * @param IAppConfig         $appConfig    The app config interface
     * @param IAppManager        $appManager   The app manager (to check OpenRegister presence)
     * @param IGroupManager      $groupManager The group manager (admin check)
     * @param ContainerInterface $container    The DI container (lazy-resolves
     *                                         OpenRegister's ObjectService so
     *                                         the app still loads when
     *                                         OpenRegister is absent)
     *
     * @return void
     *
     * @spec openspec/changes/example-change/tasks.md#task-5
     */
    public function __construct(
        private readonly IAppConfig $appConfig,
        private readonly IAppManager $appManager,
        private readonly IGroupManager $groupManager,
        private readonly ContainerInterface $container,
    ) {
    }//end __construct()

    /**
     * Delete a Booking object, enforcing per-object authorization.
     *
     * Callers must be either a Nextcloud group admin, the OpenRegister
     * creator of the booking object (`@self.owner`), or the user for whom
     * the booking was made (`booking.user`). Returns a STATUS_* constant
     * the controller maps to the right HTTP response:
     *
     * - STATUS_DELETED     → 204 No Content
     * - STATUS_NOT_FOUND   → 404
     * - STATUS_FORBIDDEN   → 403
     * - STATUS_UNAVAILABLE → 503 (OpenRegister missing)
     *
     * @param string $id     UUID of the Booking object to delete
     * @param string $userId UID of the acting user (always backend-derived,
     *                       NEVER taken from a request parameter)
     *
     * @return string One of the STATUS_* constants.
     *
     * @spec openspec/changes/example-change/tasks.md#task-5
     */
    public function delete(string $id, string $userId): string
    {
        if ($this->appManager->isInstalled('openregister') === false) {
            return self::STATUS_UNAVAILABLE;
        }

        $objectService = $this->container->get('OCA\OpenRegister\Service\ObjectService');

        $register = $this->appConfig->getValueString(Application::APP_ID, 'register', '');
        if ($register === '') {
            // App isn't configured yet — no register to delete from.
            return self::STATUS_NOT_FOUND;
        }

        $object = $objectService->find(register: $register, schema: self::SCHEMA_BOOKING, id: $id);
        if ($object === null) {
            // OWASP A01: return 404 for non-existent objects — do NOT 403, which
            // would leak existence. Only returned once we know the object is not there.
            return self::STATUS_NOT_FOUND;
        }

        if ($this->isAuthorized(object: $object, userId: $userId) === false) {
            return self::STATUS_FORBIDDEN;
        }

        $objectService->delete(register: $register, schema: self::SCHEMA_BOOKING, id: $id);

        return self::STATUS_DELETED;
    }//end delete()

    /**
     * Check whether the user is allowed to mutate the given booking object.
     *
     * Authorization rule: caller is a Nextcloud group admin, OR is the
     * OpenRegister creator of the object (via `@self.owner`), OR is the
     * user for whom the booking was made (via `booking.user`).
     *
     * The distinction between creator and booking subject matters when an
     * office manager creates a booking on behalf of another user — the
     * booking subject must be able to cancel their own reservation.
     *
     * @param object|array<string,mixed> $object The object as returned by OpenRegister
     *                                           (may be an entity or an associative array)
     * @param string                     $userId The acting user's UID
     *
     * @return bool True if authorized, false otherwise.
     *
     * @spec openspec/changes/example-change/tasks.md#task-5
     */
    private function isAuthorized(object|array $object, string $userId): bool
    {
        if ($this->groupManager->isAdmin($userId) === true) {
            return true;
        }

        // Allow the creator (@self.owner) to manage the booking.
        $owner = $this->extractOwner(object: $object);
        if ($owner !== null && $owner === $userId) {
            return true;
        }

        // Also allow the booking subject (booking.user) to cancel their own reservation.
        $bookingUser = $this->extractBookingUser(object: $object);
        return ($bookingUser !== null && $bookingUser === $userId);
    }//end isAuthorized()

    /**
     * Extract the owner UID from an OpenRegister object, tolerating both
     * the entity shape (getOwner() / jsonSerialize()['@self']['owner'])
     * and the plain associative-array shape.
     *
     * @param object|array<string,mixed> $object The object to inspect
     *
     * @return string|null The owner UID, or null if it cannot be determined.
     *
     * @spec openspec/changes/example-change/tasks.md#task-5
     */
    private function extractOwner(object|array $object): ?string
    {
        if (is_array($object) === true) {
            $self = ($object['@self'] ?? []);
            if (is_array($self) === true) {
                $v = ($self['owner'] ?? null);
                // Mirror the object-path's is_string() guard: non-string values
                // (e.g. entity refs) return null rather than failing silently.
                if (is_string($v) === true) {
                    return $v;
                }

                return null;
            }

            return null;
        }

        if (method_exists($object, 'getOwner') === true) {
            $owner = $object->getOwner();
            if (is_string($owner) === true) {
                return $owner;
            }

            return null;
        }

        if (method_exists($object, 'jsonSerialize') === true) {
            $serialised = $object->jsonSerialize();
            if (is_array($serialised) === true && isset($serialised['@self']['owner']) === true) {
                $v = $serialised['@self']['owner'];
                if (is_string($v) === true) {
                    return $v;
                }

                return null;
            }
        }

        return null;
    }//end extractOwner()

    /**
     * Extract the booking subject UID from the booking.user field.
     *
     * This is the user for whom the booking was made, which may differ from
     * the creator when an office manager creates a booking on behalf of another.
     *
     * @param object|array<string,mixed> $object The booking object as returned by OpenRegister
     *
     * @return string|null The booking subject UID, or null if not present.
     *
     * @spec openspec/changes/example-change/tasks.md#task-5
     */
    private function extractBookingUser(object|array $object): ?string
    {
        if (is_array($object) === true) {
            $v = ($object['user'] ?? null);
            if (is_string($v) === true) {
                return $v;
            }

            return null;
        }

        if (method_exists($object, 'getUser') === true) {
            $user = $object->getUser();
            if (is_string($user) === true) {
                return $user;
            }

            return null;
        }

        if (method_exists($object, 'jsonSerialize') === true) {
            $serialised = $object->jsonSerialize();
            if (is_array($serialised) === true && isset($serialised['user']) === true) {
                $v = $serialised['user'];
                if (is_string($v) === true) {
                    return $v;
                }
            }
        }

        return null;
    }//end extractBookingUser()
}//end class
