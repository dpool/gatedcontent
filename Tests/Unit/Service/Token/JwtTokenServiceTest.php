<?php

namespace Dpool\Gatedcontent\Tests\Unit\Service\Token;

use Dpool\Gatedcontent\Domain\Model\UserData;
use Dpool\Gatedcontent\Service\Token\JwtTokenConfiguration;
use Dpool\Gatedcontent\Service\Token\JwtTokenService;
use InvalidArgumentException;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use PHPUnit\Framework\TestCase;

class JwtTokenServiceTest extends TestCase
{
    protected $jwtTokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $jwtTokenConfig = (new JwtTokenConfiguration())->getConfiguration();
        $this->jwtTokenService = new JwtTokenService($jwtTokenConfig);
    }

    public function testEncodeAndDecode(): void
    {
        $userData = new UserData();
        $userData->setLastName('lastName_test');

        $encoded = $this->jwtTokenService->encode($userData, 'test', 99, 3600);
        self::assertIsString($encoded);

        $decoded = $this->jwtTokenService->decode($encoded);
        self::assertInstanceOf(Token::class, $decoded);

        $userData = $this->jwtTokenService->extractUserData($encoded);
        self::assertEquals('lastName_test', $userData->getLastName());

        $pageUid = $this->jwtTokenService->extractContentPageUid($encoded);
        self::assertEquals(99, $pageUid);
    }

    public function testValidationAction(): void
    {
        $this->expectException(RequiredConstraintsViolated::class);
        $userData = new UserData();
        $userData->setLastName('lastName_test');
        $encoded = $this->jwtTokenService->encode($userData, 'test', 99, 3600);
        $this->jwtTokenService->validate($encoded, 'test-wrong');
    }

    public function testValidationToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $userData = new UserData();
        $userData->setLastName('lastName_test');
        $encoded = 'wrong-token';
        $this->jwtTokenService->validate($encoded, 'test');
    }

    public function testValidationTTL(): void
    {
        $this->expectException(RequiredConstraintsViolated::class);
        $userData = new UserData();
        $userData->setLastName('lastName_test');
        $encoded = $this->jwtTokenService->encode($userData, 'test', 99, 2);
        sleep(3);
        $this->jwtTokenService->validate($encoded, 'test');
    }
}
