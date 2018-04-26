<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Configuration;

use LM\AuthAbstractor\Model\IMember;
use LM\AuthAbstractor\Model\IU2fRegistration;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * This is an interface that is used throughout the library to get access
 * to the environment of the application.
 *
 * By using an interface, the application
 * retains liberty on the way it wants to implement certain features, e.g. the
 * storage system used.
 *
 * @see \LM\AuthAbstractor\Implementation\ApplicationConfiguration for a
 * convenience implementation.
 */
interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    /**
     * @api
     *
     * This method must return the application ID of the application. It is
     * necessary for U2F challenges to work.
     *
     * An application ID is a term specific to the FIDO U2F specifications. It
     * can be a mobile application ID (with  format different for Android and
     * for Apple devices). It can otherwise consists of the origin of the
     * website (HTTPS URL, followed by the domain name, and the port).
     *
     * The application can possess several applications IDs (if it has several
     * domains and/or a mobile application). In that case, the application ID
     * should be an HTTPS link to the page holding a list of all the application
     * IDs associated with the application.
     *
     * @link https://fidoalliance.org/specs/fido-u2f-v1.2-ps-20170411/fido-u2f-overview-v1.2-ps-20170411.pdf
     * @return string The application ID.
     */
    public function getAppId(): string;

    /**
     * @api
     * @return string The root directory of composer
     * (e.g. /var/www/html/myapp/vendor).
     */
    public function getComposerDir(): string;

    /**
     * @api
     * @return null|string The full path to a folder containing custom Twig
     * templates that will override, when present, auth-abstractor's own
     * templates. It can return null.
     */
    public function getCustomTwigDir(): ?string;

    /**
     * @api
     * @return string The full path to the root directory of auth-abstractor.
     * E.g. /var/www/html/myapp/vendor/matthewslouismarie/auth-abstractor.
     */
    public function getLibDir(): string;

    /**
     * @api
     * @param string $username The username of the member to look for.
     * @return IMember The retrieved member.
     * @todo It should return null if the member does not exist?
     */
    public function getMember(string $username): IMember;

    /**
     * @api
     * @param string $username The username of the member.
     * @return IU2fRegistration[] The member's U2F regisrations.
     */
    public function getU2fRegistrations(string $username): array;

    /**
     * @api
     * @return array An array of settings for password requirements. It should
     * define the keys: min_length (int), enforce_min_length (bool), uppercase
     * (bool), special_chars (bool) and numbers (bool).
     * @todo This method relies on the presence of hard-coded array keys and on
     * the correctness of their associated value.
     */
    public function getPwdSettings(): array;

    /**
     * @api
     * @return TokenStorageInterface A token storage object that is used for
     * storing CSRF tokens. If you do not want to roll your own implementation
     * of TokenStorageInterface, you can simply return a
     * NativeSessionTokenStorage object that relies on PHP native sesions.
     * @see \Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage
     */
    public function getTokenStorage(): TokenStorageInterface;

    /**
     * @api
     * @param string $username The username of the member that may or may not
     * exist.
     * @return bool Whether a member with this username exists or not.
     */
    public function isExistingMember(string $username): bool;
}
