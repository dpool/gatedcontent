<?php

namespace Dpool\Gatedcontent\Service;

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;

/**
 * Class MailService
 *
 * @package Dpool\Gatedcontent\Service
 */
class MailService
{
    /**
     * @var MailMessage
     */
    protected $mailMessage;

    /**
     * MailService constructor.
     */
    public function __construct()
    {
        $this->mailMessage = GeneralUtility::makeInstance(MailMessage::class);
    }

    /**
     * @param string $subject
     * @param string $body
     * @param array|string $to
     */
    public function sendMail(string $subject, string $body, string $to): void
    {
        $this->mailMessage
            ->subject($subject)
            ->setFrom(MailUtility::getSystemFrom())
            ->from(Address::create(...MailUtility::getSystemFrom()))
            ->to(Address::create($to))
            ->html($body, 'text/html');

        // send mail object
        $this->mailMessage->send();
    }
}
