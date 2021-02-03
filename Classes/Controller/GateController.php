<?php

namespace Dpool\Gatedcontent\Controller;

use Dpool\Gatedcontent\Service\TokenServiceInterface;
use Dpool\Gatedcontent\Settings\FlexformSettings;
use TYPO3\CMS\Core\Resource\File;
use Dpool\Gatedcontent\Domain\Model\UserData;
use Dpool\Gatedcontent\Domain\Repository\UserDataRepository;
use Dpool\Gatedcontent\Event\ProcessUserDataEvent;
use Dpool\Gatedcontent\Service\MailService;
use Dpool\Gatedcontent\Service\Token\JwtTokenService;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/***************************************************************
 *  Copyright notice
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * GateController
 */
class GateController extends ActionController
{
    const DOUBLE_OPT_IN         = 'handleDoubleOptIn';
    const ADMIN_CONFIRMATION    = 'handleAdminConfirmation';
    const GATED_CONTENT_ACCESS  = 'deliverGatedContent';

    /** @var UserDataRepository */
    protected $userDataRepository;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var PersistenceManager */
    protected $persistenceManager;

    /** @var MailService */
    protected $mailService;

    /** @var JwtTokenService */
    protected $tokenService;

    /** @var FrontendInterface */
    protected $cache;

    /** @var FlexformSettings $flexformSettings */
    protected $flexformSettings;

    public function initializeAction(): void
    {
        $this->flexformSettings = new FlexformSettings($this->settings['flexform']);
    }

    /**
     * @param FrontendInterface $cache
     */
    public function injectCache(FrontendInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @param UserDataRepository $userDataRepository
     */
    public function injectUserDataRepository(UserDataRepository $userDataRepository): void
    {
        $this->userDataRepository = $userDataRepository;
    }

    public function injectFileRepository(FileRepository $fileRepository): void
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param MailService $mailService
     */
    public function injectMailService(MailService $mailService): void
    {
        $this->mailService = $mailService;
    }

    /**
     * @param TokenServiceInterface $tokenService
     */
    public function injectTokenService(TokenServiceInterface $tokenService): void
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Main action to display the form, form errors and responses after link execution in mails
     *
     * @param UserData|null $userData
     * @param bool          $triggerModal
     * @param string        $modalContext
     */
    public function formAction(UserData $userData = null, bool $triggerModal = false, $modalContext = ''): void
    {
        // assign error message if there is a validation error
        if ($this->request->getOriginalRequest() !== null) {
            $this->view->assign('validationError', true);
        }

        $this->view->assign('userData', $userData);
        // assign the setting if the modal should be triggered
        $this->view->assign('triggerModal', (int) $triggerModal);
        // assign the context for the modal which is used in the translation files to display the messages
        $this->view->assign('modalContext', $modalContext);

        // get the current contentObj and its data array
        $data = $this->configurationManager->getContentObject()->data;

        // assign additional information of the contentObj to the view
        $this->view->assign('tx_gatedcontent_header', $data['tx_gatedcontent_header']);
        $this->view->assign('tx_gatedcontent_subheader', $data['tx_gatedcontent_subheader']);
        $this->view->assign('tx_gatedcontent_description', $data['tx_gatedcontent_description']);

        $files = $this->fileRepository->findByRelation('tt_content', 'tx_gatedcontent_image', $data['uid']);
        $this->view->assign('gatedcontent_images', $files);

        // get the settings for the template layout defined in flexform and assign to the view
        $this->view->assign('templateLayout', (int)($this->settings['flexform']['gatedcontent']['template'] ?? 1));
    }

    /**
     * Handle the valid user data form and proceed data in handleGatedContentProcess method
     *
     * @param UserData|null $userData
     *
     * @throws \JsonException
     * @throws StopActionException
     */
    public function processFormAction(UserData $userData = null): void
    {
        // check if we have valid userData, if not return to form action
        if (!$userData) {
            $this->redirect('form');
        }

        $pid = $this->configurationManager->getContentObject()->data['pid'];
        $userData->setPid($pid);

        $identifier = $this->flexformSettings->gatedContentIdentifier()
            ?? (string) $this->configurationManager->getContentObject()->data['uid'];
        $userData->setIdentifier($identifier);

        // Option 1: if doubleOptIn is enabled generate doubleOptIn link and send mail
        if ($this->flexformSettings->isDoubleOptInEnabled()) {
            $this->sendDoubleOptInMail($userData);
            $this->redirectToFormModal('doubleOptInConfirmation');
        }

        // Option 2: if adminConfirmation is enabled generate adminConfirmation link and send mail
        if ($this->flexformSettings->isAdminConfirmationEnabled()) {
            $this->sendAdminConfirmationMail($userData);
            $this->redirectToFormModal('adminConfirmationPending');
        }

        // @TODO: THIS only once - what if page reload?
        $this->runFinisherActions($userData);

        $this->deliverGatedContent($userData);
    }

    /**
     * Action which is called after clicking the link in the doubleOptIn mail;
     * Function checks the validity of the token by cache and token validation, afterwards the setting for adminConfirmation is checked
     * If adminConfirmation is not enabled this function redirects to the deliverGatedContent function
     *
     * @param string $token
     *
     * @throws \JsonException
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function handleDoubleOptInAction(string $token): void
    {
        $userData = $this->validateAndDecodeToken($token, self::DOUBLE_OPT_IN);

        // If we need admin confirmation and if the user did not click the link before,
        // we send the mail to the admin with a link to grant access to the content.
        if ($this->flexformSettings->isAdminConfirmationEnabled() && !$this->tokenHasBeenUsedBefore($token)) {
            $this->sendAdminConfirmationMail($userData);
            $this->redirectToFormModal('adminConfirmationPending');
            // returns
        }

        // Otherwise we can show the gated content.
        // If the double-opt-in link is clicked the first time, we store the user data.
        if (!$this->tokenHasBeenUsedBefore($token)) {
            $this->runFinisherActions($userData);
        }

        // We deliver the gated content no matter how often the user
        // clicks the double-opt-in link.
        $this->deliverGatedContent($userData);
    }

    /**
     * Action which is called after clicking the link in the adminConfirmation mail;
     * Function checks the validity of the token by token validation, afterwards an email is send to the user containing the link to the gated content
     * After sending the mail a message (adminConfirmationApproved) is shown to the admin
     *
     * @param string $token
     *
     * @throws \JsonException
     * @throws StopActionException
     */
    public function handleAdminConfirmationAction(string $token): void
    {
        $userData = $this->validateAndDecodeToken($token, self::ADMIN_CONFIRMATION);

        // send a mail to the user allowing access to the content
        if (!$this->tokenHasBeenUsedBefore($token)) {
            $this->sendDeliverGatedContentMail($userData);
            $this->redirectToFormModal('adminConfirmationApproved');
        }
    }

    /**
     * @param string $token
     *
     * @throws IllegalObjectTypeException
     * @throws \JsonException
     * @throws StopActionException
     */
    public function deliverGatedContentAction(string $token): void
    {
        $userData = $this->validateAndDecodeToken($token, self::GATED_CONTENT_ACCESS);

        // If the user has clicked the link to the content for the
        // first time, we store the user's data.
        if (!$this->tokenHasBeenUsedBefore($token)) {
            $this->runFinisherActions($userData);
        }

        $this->deliverGatedContent($userData);
    }

    /**
     * Store the user data
     *
     * Ensure this only runs once!
     *
     * @param UserData $userData
     *
     * @throws IllegalObjectTypeException
     */
    protected function runFinisherActions(UserData $userData): void
    {
        // We store the data in the database if set in the settings
        if ($this->flexformSettings->shouldSaveUserDataToDatabase()) {
            $this->userDataRepository->add($userData);
            $this->persistenceManager->persistAll();
        }

        // We send the data per mail if a recipient is provided in the settings
        if ($this->flexformSettings->finisherEmailRecipient()) {
            $this->sendFinisherMail($userData);
        }
    }

    /**
     * Deliver the gated content to the user
     *
     * @param UserData $userData
     *
     * @throws StopActionException
     */
    protected function deliverGatedContent(UserData $userData): void
    {
        // before actually delivering the content add psr event dispatcher
        $this->eventDispatcher->dispatch(new ProcessUserDataEvent($userData));

        // check if we need to deliver a file
        if ($this->flexformSettings->shouldDeliverGatedContentFile()) {
            $errorCode = 'error.missingFile';
            // check if the file exists and is valid. Is so, trigger the download. Otherwise redirect and show error message
            if ($this->settings['flexform']['gatedcontent']['file'] > 0) {
                $data = $this->configurationManager->getContentObject()->data;
                $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
                $fileObjects = $fileRepository->findByRelation('tt_content', 'gatedcontent_file', $data['uid']);
                if (!isset($fileObjects[0])) {
                    //return to form action and show error
                    $this->redirectToFormModal($errorCode);
                }
                // file object exists, so hand it over to downloadFile function and get stream
                $downloadFile = $fileObjects[0];
                $this->downloadFile($downloadFile->getOriginalFile());
            }
            $this->redirectToFormModal($errorCode);
        }

        // if we need to deliver a page build the link to the gated content page and execute the redirect
        $contentPageUid = $this->flexformSettings->gatedContentPageUid();

        $redirectUri = $this->uriBuilder
            ->reset()
            ->setNoCache(true)
            ->setLinkAccessRestrictedPages(true)
            ->setTargetPageUid($contentPageUid)
            ->setArguments([
                'token' => $this->tokenService->encode(
                    $userData,
                    'showMagicContent',
                    $contentPageUid,
                    $this->getValidityPeriodByTsSettings()
                ),
                'logintype' => 'login',
            ])
            ->build();

        $this->redirectToUri($redirectUri);
    }

    /********************************************************************************
     * Mails
     ********************************************************************************/

    /**
     * Send a mail with a double-opt-in link to the user, verifying the user's email address
     *
     * @param UserData $userData
     *
     * @throws \JsonException
     */
    protected function sendDoubleOptInMail(UserData $userData): void
    {
        $body = $this->getMailBody(
            $this->flexformSettings->doubleOptInEmailBody(),
            $userData,
            $this->generateLink(self::DOUBLE_OPT_IN, $userData)
        );

        // send mail to user containing the link and the parsed marker-body
        $this->mailService->sendMail(
            $this->flexformSettings->doubleOptInEmailSubject(),
            $body,
            $userData->getEmail()
        );
    }

    /**
     * Method to retrieve the link for adminConfirmation and sending the mail to the admin
     *
     * @param UserData $userData
     *
     * @throws \JsonException
     */
    protected function sendAdminConfirmationMail(UserData $userData): void
    {
        $body = $this->getMailBody(
            $this->flexformSettings->adminConfirmationEmailToAdminBody(),
            $userData,
            $this->generateLink(self::ADMIN_CONFIRMATION, $userData)
        );

        // send mail to admin
        $this->mailService->sendMail(
            $this->flexformSettings->adminConfirmationEmailToAdminSubject(),
            $body,
            $this->flexformSettings->adminConfirmationEmailRecipient()
        );
    }

    /**
     * @param UserData $userData
     *
     * @throws \JsonException
     */
    protected function sendDeliverGatedContentMail(UserData $userData): void
    {
        $body = $this->getMailBody(
            $this->flexformSettings->adminConfirmationEmailToUserBody(),
            $userData,
            $this->generateLink(self::GATED_CONTENT_ACCESS, $userData)
        );

        // send mail to user
        $this->mailService->sendMail(
            $this->flexformSettings->adminConfirmationEmailToUserSubject(),
            $body,
            $userData->getEmail()
        );
    }

    /**
     * @param UserData $userData
     */
    protected function sendFinisherMail(UserData $userData): void
    {
        $body = $this->getMailBody(
            $this->flexformSettings->finisherEmailBody(),
            $userData,
            ''
        );

        $this->mailService->sendMail(
            $this->flexformSettings->finisherEmailSubject(),
            $body,
            $this->flexformSettings->finisherEmailRecipient()
        );
    }

    /********************************************************************************
     * Helper methods
     ********************************************************************************/

    /**
     * Get the mail body, with the markers replaced
     *
     * @param string   $template
     * @param UserData $userData
     * @param string   $url
     *
     * @return string
     */
    protected function getMailBody(string $template, UserData $userData, string $url): string
    {
        $marker = $this->getEmailMarkerArray($userData, $url);

        return str_replace(
            array_keys($marker),
            array_values($marker),
            $template
        );
    }

    /**
     * Generate a link to the required action, containing the access token for the next step
     *
     * @param string   $action
     * @param UserData $userData
     *
     * @return string
     *
     * @throws \JsonException
     */
    protected function generateLink(string $action, UserData $userData): string
    {
        $currentPid = $this->configurationManager->getContentObject()->data['pid'];

        // retrieve token containing the userData, the action string and a validity timestamp
        $token = $this->tokenService->encode(
            $userData,
            $action,
            $this->flexformSettings->gatedContentPageUid(),
            $this->getValidityPeriodByTsSettings()
        );

        // now build the url and return it
        return $this->uriBuilder
            ->reset()
            ->setNoCache(true)
            ->setTargetPageUid($currentPid)
            ->setCreateAbsoluteUri(true)
            ->uriFor($action, ['token' => $token], 'Gate');
    }

    /**
     * Build the marker (search-replace) array which is used to parse the mail body
     * Valid markers are each getter of the UserData object, the link and the content element identifier
     *
     * @param UserData $userData
     * @param string   $url
     *
     * @return array
     */
    protected function getEmailMarkerArray(UserData $userData, string $url = ''): array
    {
        // build a [search => replace] array

        $markerArray = [ '{identifier}' => $userData->getIdentifier() ];

        if (trim($url)) {
            $linkText = LocalizationUtility::translate('tx_gatedcontent.linkTitle', 'gatedcontent');
            $link     = sprintf('<a href="%s" target="_blank">%s</a>', $url, $linkText);

            $markerArray['{link}'] = $link;
        }

        // add replacements for the user data
        // [
        //  {user.lastname} => $userData->getLastName(),
        //  {user.email}    => $userData->getEmail(),
        //  ...
        // ]

        $methods = array_filter(get_class_methods($userData), function ($method) {
            return strpos($method, 'get') === 0;
        });

        foreach ($methods as $method) {
            $key = sprintf('{user.%s}', strtolower(substr($method, 3)));
            $markerArray[$key] = $userData->$method();
        }

        return $markerArray;
    }

    /**
     * Internal function to call the validateAndDecodeToken method of the tokenService
     * if an exception is thrown return to the form and show error code
     *
     * @param string $token
     * @param string $action
     *
     * @return UserData
     *
     * @throws StopActionException
     */
    protected function validateAndDecodeToken(string $token, string $action): UserData
    {
        // try to receive the validated and decoded token
        try {
            $this->tokenService->validate($token, $action);
        } catch (\Exception $e) {
            // redirect to formAction and trigger modal if validation throws an error
            $this->redirectToFormModal('error.invalidToken');
        }

        return $this->tokenService->extractUserData($token);
    }

    /**
     * Redirects to the form action, displaying a modal
     *
     * @param string $modalContext
     *
     * @throws StopActionException
     */
    protected function redirectToFormModal(string $modalContext): void
    {
        // build arguments by enabling the modal and setting the modal context
        // return to the form action
        $this->redirect(
            'form',
            'Gate',
            null,
            ['triggerModal' => true, 'modalContext' => $modalContext]
        );
    }

    /**
     * Method reads the validity period setting defined in TS. If value is not set the default of 1 hour is used
     *
     * @return int
     */
    protected function getValidityPeriodByTsSettings(): int
    {
        return (int) ($this->settings['tokenService']['validityPeriod'] ?? 3600);
    }

    /**
     * Sends the given file to the user
     *
     * @param File $file
     */
    private function downloadFile(File $file): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file->getMimeType());
        header('Content-Disposition: attachment; filename=' . $file->getName());
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $file->getSize());

        ob_clean();
        flush();
        echo $file->getContents();

        exit();
    }

    /**
     * Check if a token has been used before
     * Used to avoid that multiple mails are sent, e. g. if a user clicks the link multiple times
     *
     * We use the cache to keep a blacklist of then-unusable tokens.
     * The cache might be cleared, but this should be okay for our use-case.
     *
     * @param string $token
     *
     * @return bool
     */
    protected function tokenHasBeenUsedBefore(string $token): bool
    {
        // Use a sha1 to shorten the JWT
        $cacheIdentifier = sha1($token);

        // If the token has been used before, it is not valid anymore
        if ($this->cache->has($cacheIdentifier)) {
            return true;
        }

        // Blacklist (and invalidate) the token
        $this->cache->set($cacheIdentifier, $token, [], 0);

        return false;
    }
}
