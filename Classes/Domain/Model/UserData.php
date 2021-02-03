<?php
declare(strict_types=1);

namespace Dpool\Gatedcontent\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * UserData
 */
class UserData extends AbstractEntity
{
    /**
     * First name
     * @var string
     */
    protected $firstName = '';

    /**
     * Last name
     * @var string
     */
    protected $lastName = '';

    /**
     * Company
     * @var string
     */
    protected $company = '';

    /**
     * Telephone Number
     * @var string
     */
    protected $telephone = '';

    /**
     * Email
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("EmailAddress")
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $email = '';

    /**
     * Zip code
     * @var string
     */
    protected $zip = '';

    /**
     * City
     * @var string
     */
    protected $city = '';

    /**
     * Identifier
     * @var string
     */
    protected $identifier = '';

    /**
     * Newsletter subscription
     * @var bool
     */
    protected $newsletterSubscription = false;

    /**
     * @var bool
     * @TYPO3\CMS\Extbase\Annotation\Validate("Boolean", options={"is": true})
     */
    protected $acceptPrivacyPolicy = false;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone(string $telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return bool
     */
    public function getNewsletterSubscription(): bool
    {
        return $this->newsletterSubscription;
    }

    /**
     * @param bool $newsletterSubscription
     */
    public function setNewsletterSubscription(bool $newsletterSubscription)
    {
        $this->newsletterSubscription = $newsletterSubscription;
    }

    /**
     * @return bool
     */
    public function isAcceptPrivacyPolicy(): bool
    {
        return $this->acceptPrivacyPolicy;
    }

    /**
     * @param bool $acceptPrivacyPolicy
     */
    public function setAcceptPrivacyPolicy(bool $acceptPrivacyPolicy)
    {
        $this->acceptPrivacyPolicy = $acceptPrivacyPolicy;
    }
}
