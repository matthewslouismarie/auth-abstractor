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
 * @todo This class is too heavily tied to specfic challenges.
 */
interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    /**
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
     * @api
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
     * This must return null if the CA verification is disabled, or an array
     * of trusted CA filenames.
     *
     * CA filenames have the .pem format. All U2F tokens have a certificate.
     * This certificate is common to a batch of U2F tokens and is normally not
     * sufficient to identity a unique U2F token. You can specify an array of
     * certificate filenames to only accept users who use certain brands of U2F
     * tokens. Alternatively, if you return null, you will accept all brands of
     * U2F tokens.
     *
     * @api
     * @example tests/certificates/yubico.pem
     * @return null|string[] An array of filenames containing certificates, or
     * null if the certificate validation is disabled.
     * @todo This is coupled with U2F challenges, it should be challenge-
     * agnostic.
     */
    public function getU2fCertificates(): ?array;

    /**
     * @api
     * @param string $username The username of the member.
     * @return IU2fRegistration[] The member's U2F regisrations.
     * @todo This is coupled with U2F registrations.
     */
    public function getU2fRegistrations(string $username): array;

    /**
     * @api
     * @return array An array of settings for password requirements. It should
     * define the keys: min_length (int), enforce_min_length (bool), uppercase
     * (bool), special_chars (bool) and numbers (bool).
     * @todo This method relies on the presence of hard-coded array keys and on
     * the correctness of their associated value.
     * @todo This is coupled to Password challenges.
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
