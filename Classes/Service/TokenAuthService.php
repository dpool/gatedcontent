<?php

namespace Dpool\Gatedcontent\Service;

use Exception;
use TYPO3\CMS\Core\Authentication\AbstractAuthenticationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TokenAuthService extends AbstractAuthenticationService
{
    /** @var TokenServiceInterface */
    protected $tokenService;

    public function __construct()
    {
        $container = GeneralUtility::getContainer();
        $this->tokenService = $container->get('tokenService');
    }

    /**
     * @return string|null
     */
    protected function getRequestToken()
    {
        if (!array_key_exists('token', $_REQUEST)) {
            return null;
        }

        return $_REQUEST['token'];
    }

    /**
     * @param array $user
     *
     * @return int
     */
    public function authUser(array $user): int
    {
        // We only care about frontend authentication
        if ($this->mode !== 'authUserFE' || !$this->getRequestToken()) {
            return 100;
        }

        return 200;
    }

    /**
     * Resolve a user to login.
     * Our user who can access the page gets the pseudo ID 0,
     * and gets the usergroup set in the access tab of the page.
     * @return array|false User data array
     */
    public function getUser()
    {
        $encodedToken = $this->getRequestToken();

        if ($this->mode !== 'getUserFE' || !$encodedToken) {
            return false;
        }

        try {
            $this->tokenService->validate($encodedToken, 'showMagicContent');
        } catch (Exception $e) {
            return false;
        }

        $feGroup = $this->getFeGroupFromPage($this->tokenService->extractContentPageUid($encodedToken));

        if (!$feGroup) {
            return false;
        }

        return [
            'uid' => 0,
            'usergroup' => $feGroup,
        ];
    }

    /**
     * Get the frontend groups needed to access a page.
     *
     * @param int $pageUid
     *
     * @return string|false
     */
    protected function getFeGroupFromPage(int $pageUid)
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable('pages');

        // Resolve the frontend groups
        // which have access to the page from the database.
        $feGroup = $connection->select(
            ['fe_group'],
            'pages',
            ['uid' => $pageUid]
        )->fetchColumn(0);

        return $feGroup;
    }
}
