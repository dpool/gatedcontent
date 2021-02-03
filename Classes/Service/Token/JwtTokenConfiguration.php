<?php

namespace Dpool\Gatedcontent\Service\Token;

use Lcobucci\JWT\Signer\{Hmac, Key};
use Lcobucci\JWT\Configuration;

class JwtTokenConfiguration
{
    public function getConfiguration()
    {
        $key = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];

        return Configuration::forSymmetricSigner(
            new Hmac\Sha256(),
            Key\InMemory::plainText($key)
        );
    }
}