<?php


namespace Dpool\Gatedcontent\Settings;


class FlexformSettings
{
    public const DELIVER_METHOD_FILE = 'file';
    public const DELIVER_METHOD_PAGE = 'page';

    /** @var array */
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /********************************************************************************
     * Double opt in
     ********************************************************************************/

    public function isDoubleOptInEnabled(): bool
    {
        return (bool) $this->settings['doubleOptIn']['enable'];
    }

    public function doubleOptInEmailSubject(): string
    {
        return $this->settings['doubleOptIn']['emailSubject'];
    }

    public function doubleOptInEmailBody(): string
    {
        return $this->settings['doubleOptIn']['emailBody'];
    }

    /********************************************************************************
     * Admin confirmation
     ********************************************************************************/

    public function isAdminConfirmationEnabled(): bool
    {
        return (bool) $this->settings['adminConfirmation']['enable'];
    }

    public function adminConfirmationEmailToAdminSubject(): string
    {
        return $this->settings['adminConfirmation']['emailSubject']['admin'];
    }

    public function adminConfirmationEmailToAdminBody(): string
    {
        return $this->settings['adminConfirmation']['emailBody']['admin'];
    }

    public function adminConfirmationEmailToUserSubject(): string
    {
        return $this->settings['adminConfirmation']['emailSubject']['user'];
    }

    public function adminConfirmationEmailToUserBody(): string
    {
        return $this->settings['adminConfirmation']['emailBody']['user'];
    }

    public function adminConfirmationEmailRecipient(): string
    {
        return $this->settings['adminConfirmation']['emailRecipient'];
    }

    /********************************************************************************
     * Gated content
     ********************************************************************************/

    public function gatedContentPageUid(): int
    {
        return (int) $this->settings['gatedcontent']['pid'];
    }

    public function gatedContentIdentifier(): string
    {
        return (string) $this->settings['gatedcontent']['identifier'];
    }

    public function gatedContentDeliverMethod(): string
    {
        return (string) $this->settings['gatedcontent']['deliverMethod'];
    }

    public function gatedContentFile(): string
    {
        return (string) $this->settings['gatedcontent']['file'];
    }

    public function shouldDeliverGatedContentFile(): bool
    {
        return $this->gatedContentDeliverMethod() === self::DELIVER_METHOD_FILE;
    }

    /********************************************************************************
     * Finisher
     ********************************************************************************/

    public function shouldSaveUserDataToDatabase(): bool
    {
        return (bool) $this->settings['finisher']['saveToDB']['enable'];
    }

    public function finisherEmailSubject(): string
    {
        return $this->settings['finisher']['emailSubject'];
    }

    public function finisherEmailBody(): string
    {
        return $this->settings['finisher']['emailBody'];
    }

    /**
     * @return string|null
     */
    public function finisherEmailRecipient()
    {
        return $this->settings['finisher']['emailRecipient'];
    }
}