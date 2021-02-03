<?php

namespace Dpool\Gatedcontent\Service\Token;

use DateTimeImmutable;
use Dpool\Gatedcontent\Domain\Model\UserData;
use Dpool\Gatedcontent\Service\TokenServiceInterface;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use RuntimeException;


/**
 * Class JwtTokenService
 * @package Dpool\Gatedcontent\Service\Token
 */
class JwtTokenService implements TokenServiceInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * JwtTokenService constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Encode a JWT
     * @see https://auth0.com/docs/tokens/json-web-tokens/json-web-token-claims
     *
     * @inheritDoc
     *
     * @throws RuntimeException
     */
    public function encode(UserData $userData, string $action, int $pageUid, int $ttl): string
    {
        $now = new DateTimeImmutable();

        return $this->configuration->builder()
            // sub
            ->relatedTo($pageUid)
            // jti
            ->identifiedBy($userData->getEmail())
            // iss
            ->issuedBy($action)
            // iat
            ->issuedAt($now)
            // exp
            ->expiresAt($now->modify("+ {$ttl} seconds"))
            ->withClaim('userData', serialize($userData))
            ->withClaim('pageUid', $pageUid)
            ->getToken(
                $this->configuration->signer(),
                $this->configuration->signingKey()
            );
    }

    /**
     * @param string $token
     *
     * @return Token
     * 
     * @throws RuntimeException
     */
    public function decode(string $token): Token
    {
        return $this->configuration->parser()->parse($token);
    }

    /**
     * @param string $token
     * @param string $action
     *
     * @return void
     */
    public function validate(string $token, string $action): void
    {
        $decoded = $this->configuration->parser()->parse($token);

        $this->configuration->validator()->assert(
            $decoded,
            new Constraint\SignedWith($this->configuration->signer(), $this->configuration->verificationKey()),
            new Constraint\IssuedBy($action),
            new Constraint\ValidAt(SystemClock::fromSystemTimezone())
        );
    }

    /**
     * @param string $token
     *
     * @return UserData
     */
    public function extractUserData(string $token): UserData
    {
        $decoded = $this->decode($token);

        return unserialize($decoded->claims()->get('userData'), [UserData::class]);
    }

    /**
     * @param string $token
     *
     * @return int
     */
    public function extractContentPageUid(string $token): int
    {
        $decoded = $this->decode($token);

        return $decoded->claims()->get('pageUid');
    }
}
