<?php

namespace Dpool\Gatedcontent\Service;

use Dpool\Gatedcontent\Domain\Model\UserData;
use Exception;

/**
 * Interface TokenServiceInterface
 * @package Dpool\Gatedcontent\Service
 */
interface TokenServiceInterface
{
    /**
     * Encode the provided data into to a string.
     *
     * @param UserData $userData The form user data
     * @param string $action Action ths token is generated for
     * @param int $pageUid Target page containing the content
     * @param int $ttl Time in seconds the token should be valid
     *
     * @return string
     */
    public function encode(UserData $userData, string $action, int $pageUid, int $ttl): string;

    /**
     * Decode a string token.
     * Return the internal representation of the data used by this service.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function decode(string $token);

    /**
     * Validate a token with the current action.
     * Should throw an exception if the token or action is invalid.
     *
     * @param string $token
     * @param string $action
     *
     * @return void
     * @throws Exception
     */
    public function validate(string $token, string $action);

    /**
     * Extract the UserData object from the token.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function extractUserData(string $token);

    /**
     * Extract the pageUid from the token.
     * Used in the auth service to find the FE groups for the page.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function extractContentPageUid(string $token);
}
