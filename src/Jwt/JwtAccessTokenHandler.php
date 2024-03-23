<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

/**
 * Description of JwtAccessTokenHandler
 *
 * @author kjell
 */
final class JwtAccessTokenHandler extends JwtHandler {

    function __construct() {

        $this->issuer = $_SERVER['SERVER_NAME'];
        $this->issuedAt = time();

        // Token Validity (3600 second = 1hr)
        $this->expire = $this->issuedAt + 3600;
        $this->expire = $this->issuedAt + 30;

        // Set your strong secret or signature
        $this->secrect = "051670d23d9b4dd85dc8632318387bb7bb1be998";
    }
}
