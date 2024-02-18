<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

/**
 * Description of JwtRefreshTokenHandler
 *
 * @author kjell
 */
final class JwtRefreshTokenHandler extends JwtHandler{
    function __construct() {

        $this->issuer = $_SERVER['SERVER_NAME'];
        $this->issuedAt = time();

        // Token Validity (1 year)
        $this->expire = (new \DateTime("next year"))->getTimestamp();
        $this->expire = (new \DateTime("tomorrow"))->getTimestamp();

        // Set your strong secret or signature
        $this->secrect = "f5aa2a50968462f49f5b02050208d148252b55f1";
    }

}
