<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "container" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Dpool\Gatedcontent\Tests\Acceptance\Support\FrontendTester;

class FrontendCest
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://web:8000/typo3temp/var/tests/acceptance/'
        ]);
    }

    /**
     * @param FrontendTester $I
     *
     * @group workspace
     */
    public function testBlank(FrontendTester $I): void
    {
        $I->amOnPage('/');
        $I->see('First name*');
    }

    /**
     * Make one complete run through every step to get the content
     *
     * @param FrontendTester $I
     *
     * @throws \Exception
     */
    public function testSuccessfulContentAccess(FrontendTester $I): void
    {
        $I->amGoingTo('test that we can not access the secured content');
        $this->assertUrlIsForbidden($I, 'secured');

        $I->amOnPage('/');
        $I->see('First name*');
        $I->see('Email*');
        $I->see('I have read the general terms and conditions and agree to them.*');

        $I->deleteAllEmails();

        // Step 1: Fill out the form.

        $I->amGoingTo('fill out the form with valid data and submit it.');

        $this->fillAndSubmitForm($I, [
            'firstName' => 'Tester_Firstname',
            'lastName'  => 'Tester_Lastname',
            'email'     => 'testuser@dpool.com',
            'company'   => 'dpool',
            'telephone' => '12345',
            'zip'       => '80469',
            'city'      => 'München'
        ]);

        $I->waitForElementVisible('.gatedContentModal', 5);
        $I->see('Thank you for your interest in our content.');

        // Step 2: Double-opt-in.

        $I->expectTo('have a mail with a double-opt-in link sent to the user.');

        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();

        $I->seeInOpenedEmailSubject('Subject double-opt-in mail');
        $I->seeInOpenedEmailBody('double-opt-in-link:');
        $I->seeInOpenedEmailRecipients('testuser@dpool.com');

        $doubleOptInMailContent = $I->grabBodyFromEmail();
        $doubleOptInHref = $this->extractLinkFromMailBody($doubleOptInMailContent);

        $I->deleteAllEmails();

        $I->amGoingTo('click the double-opt-in-link.');
        $I->amOnUrl($doubleOptInHref);
        $I->waitForElementVisible('.gatedContentModal', 5);
        $I->see('Thank you for confirming your data.');

        // Step 3: Admin confirmation.

        $I->expectTo('have a mail with a authorization link sent to the admin.');

        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();

        $I->seeInOpenedEmailSubject('Admin: Confirmation Access');
        $I->seeInOpenedEmailBody('click to confirm the access to gated-content');
        $I->seeInOpenedEmailRecipients('testadmin@dpool.com');

        $adminConfirmationMailContent = $I->grabBodyFromEmail();
        $adminConfirmationHref = $this->extractLinkFromMailBody($adminConfirmationMailContent);

        $I->deleteAllEmails();

        $I->amOnUrl($adminConfirmationHref);
        $I->waitForElementVisible('.gatedContentModal', 5);
        $I->see('You granted successfully access');

        // Step 4: Gated content access.

        $I->expectTo('have a mail with a link to the gated content sent to the user.');

        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();

        $I->seeInOpenedEmailSubject('Access confirmed');
        $I->seeInOpenedEmailBody("You can now see the requested content");
        $I->seeInOpenedEmailRecipients('testuser@dpool.com');

        $gatedContentMailContent = $I->grabBodyFromEmail();
        $gatedContentHref = $this->extractLinkFromMailBody($gatedContentMailContent);

        $I->deleteAllEmails();

        $I->amOnUrl($gatedContentHref);
        $I->seeInTitle('Testing: Secured');

        // Check the final.

        $I->expectTo('have a mail informing the content owner with the user data.');

        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();

        $I->seeInOpenedEmailSubject('Subject finisher mail');
        $I->seeInOpenedEmailBody("The following data was submitted:");
        $I->seeInOpenedEmailRecipients('testfinisher@dpool.com');

        $I->deleteAllEmails();

        // If we use the DB-extension for Codeception and define the connection in codeception.yml,
        // the extension tries to connect before our testing setup is wired up.
        // If we fix this, we could do an $I->seeInDatabase('...').
        // Meanwhile, we can test database contents on a lower level:

        $pdo = new \PDO('mysql:host=mariadb10;dbname=func_test_at', 'root', 'funcp');
        $stmt = $pdo->query('SELECT * FROM tx_gatedcontent_domain_model_userdata');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $I->assertCount(1, $result);
        // 'pid' has to be the PID of our tt_conent element:
        $I->assertEquals('1', $result[0]['pid']);
        $I->assertEquals('testuser@dpool.com', $result[0]['email']);
        // 'identifier' has to be settings.flexform.gatedcontent.identifier:
        $I->assertEquals('gated-content', $result[0]['identifier']);

        $I->expect('access to the secured page is still forbidden without token (no login)');
        $this->assertUrlIsForbidden($I, 'secured');
    }

    public function testNoMultipleMailsAreSent(FrontendTester $I)
    {
        $I->amOnPage('/');

        $this->fillAndSubmitForm($I, [
            'firstName' => 'Tester_Firstname',
            'lastName'  => 'Tester_Lastname',
            'email'     => 'testuser@dpool.com',
            'company'   => 'dpool',
            'telephone' => '12345',
            'zip'       => '80469',
            'city'      => 'München'
        ]);

        // The double opt in mail
        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);

        $I->openNextUnreadEmail();
        $doubleOptInMailContent = $I->grabBodyFromEmail();
        $doubleOptInHref = $this->extractLinkFromMailBody($doubleOptInMailContent);
        $I->deleteAllEmails();

        // The admin confirmation mail - the admin gets a mail with a link to grant access
        $I->amOnUrl($doubleOptInHref);
        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();
        $adminConfirmationMailContent = $I->grabBodyFromEmail();
        $adminConfirmationHref = $this->extractLinkFromMailBody($adminConfirmationMailContent);
        $I->deleteAllEmails();

        // Second time click on double-opt-in link does not trigger another mail
        $I->amOnUrl($doubleOptInHref);
        $I->fetchEmails();
        $I->dontHaveUnreadEmails();

        // The content access mail - the user gets a mail with a link to the content
        $I->amOnUrl($adminConfirmationHref);
        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->openNextUnreadEmail();
        $accessContentMailContent = $I->grabBodyFromEmail();
        $accessContentHref = $this->extractLinkFromMailBody($accessContentMailContent);
        $I->deleteAllEmails();

        // Second time click on admin-confirmation link does not trigger another mail
        $I->amOnUrl($adminConfirmationHref);
        $I->fetchEmails();
        $I->dontHaveUnreadEmails();

        // The finisher mail - a finisher mail is sent if the user visits the content
        $I->amOnUrl($accessContentHref);
        $I->fetchEmails();
        $I->haveNumberOfUnreadEmails(1);
        $I->deleteAllEmails();

        // Second time the user visits the content does not trigger another mail
        $I->amOnUrl($accessContentHref);
        $I->fetchEmails();
        $I->dontHaveUnreadEmails();
    }

    /**
     * Test some invalid form data and verify the form isn't submitted
     *
     * @param FrontendTester $I
     */
    public function testInvalidFormData(FrontendTester $I)
    {
        $I->amOnPage('/');

        $I->deleteAllEmails();

        $I->expect('the form can not be submitted without a first name.');

        $this->fillAndSubmitForm($I, [
            'firstName' => '',
            'lastName'  => 'Tester_Lastname',
            'email'     => 'testuser@dpool.com',
            'company'   => 'dpool',
            'telephone' => '12345',
            'zip'       => '80469',
            'city'      => 'München'
        ]);

        $I->dontSee('Thank you for your interest in our content.');

        $I->expect('the form can not be submitted with an invalid email.');

        $this->fillInput($I, 'firstName', 'Tester_Firstname');
        $this->fillInput($I, 'email', 'some-invalid-mail');
        $this->submitForm($I);

        $I->dontSee('Thank you for your interest in our content.');

        $I->expect('the form can not be submitted without acceptance of the privacy policy.');

        $this->fillInput($I, 'email', 'some-valid-mail@some-domain.org');
        $I->uncheckOption($this->inputNameFor('acceptPrivacyPolicy'));
        $this->submitForm($I);

        $I->dontSee('Thank you for your interest in our content.');

        $I->expect('the form can be submitted if everything is filled in correctly.');

        $I->checkOption($this->inputNameFor('acceptPrivacyPolicy'));
        $this->submitForm($I);

        $I->see('Thank you for your interest in our content.');
    }

    /**
     * Shortcut for input field names
     *
     * Input names are like: tx_gatedcontent_gate[userData][name].
     *
     * @param string $field
     * @return string
     */
    protected function inputNameFor(string $field)
    {
        return "tx_gatedcontent_gate[userData][${field}]";
    }

    /**
     * Shortcut to fill an input field
     *
     * @param FrontendTester $I
     * @param string $inputName
     * @param string $value
     */
    protected function fillInput(FrontendTester $I, string $inputName, string $value)
    {
        $I->fillField($this->inputNameFor($inputName), $value);
    }

    /**
     * Shortcut to submit the form
     *
     * @param FrontendTester $I
     */
    protected function submitForm(FrontendTester $I)
    {
        $I->clickWithLeftButton('button[type="submit"]');
    }

    /**
     * Fill the user data form with data from an array
     *
     * @param FrontendTester $I
     * @param array $formData
     */
    protected function fillAndSubmitForm(FrontendTester $I, array $formData)
    {
        foreach ($formData as $inputName => $value) {
            $this->fillInput($I, $inputName, $value);
        }

        $I->checkOption($this->inputNameFor('acceptPrivacyPolicy'));
        $this->submitForm($I);
    }

    /**
     * Extract the URL of a link from an email
     *
     * @param string $mailContent
     * @param string $label
     *
     * @return false|string
     */
    protected function extractLinkFromMailBody(string $mailContent)
    {
        preg_match('/<a href="(.+)">(.+)<\/a>/', $mailContent, $matches);

        // $matches[1] contains the URL and target="_blank", separated by a space.
        $href = explode(' ', $matches[1])[0];

        // We have to get rid of cHash params in our URL.
        $cHashPos = strpos($href, '&cHash');

        return substr($href, 0, $cHashPos);
    }

    /**
     * Helper method to check if a page can not be accessed
     *
     * @param FrontendTester $I
     * @param string $url
     * @param array $query
     */
    protected function assertUrlIsForbidden(FrontendTester $I, string $url, array $query = []): void
    {
        try {
            $this->client->get($url, [ 'query' => $query ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $I->expect($url . ' to be forbidden');
            $I->assertEquals(403, $e->getCode());
        }
    }
}
