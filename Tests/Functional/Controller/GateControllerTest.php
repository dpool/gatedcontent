<?php

namespace Dpool\Gatedcontent\Tests\Functional\Controller;

use Dpool\Gatedcontent\Controller\GateController;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Response;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class GateControllerTest extends FunctionalTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var GateController
     */
    protected $controller;

    protected $coreExtensionsToLoad = [
        'core',
        'extbase',
//        'fluid',
//        'backend',
//        'about',
//        'install',
//        'frontend',
//        'recordlist',
//        'fluid_styled_content',
//        'cms-rte-ckeditor'
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/gatedcontent'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../../Fixtures/pages.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tt_content.xml');

        $this->request = GeneralUtility::makeInstance(Request::class);
        $this->request->setPluginName('gatedcontent');
        $this->request->setControllerExtensionName('Dpool\\GatedContent');
        $this->request->setControllerName('Gate');
        $this->request->setMethod('GET');
        $this->request->setFormat('html');

        $this->controller = $this->getContainer()->get(GateController::class);
    }

    /**
     * @test
     */
    public function customValidatorsAreProperlyResolved()
    {
        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);

        // Setup
        $this->request->setControllerActionName('form');
        $this->request->setArgument('userData', null);
        $this->request->setArgument('triggerModal', false);
        $this->request->setArgument('modalContext', '');

        // Test run
        $this->controller->processRequest($this->request, $response);

        self::assertEquals(200, $response->getStatusCode());
        self::assertStringContainsString('from', $response->getContent());
    }

    private function buildController(): GateController
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager->get(GateController::class);
    }
}
