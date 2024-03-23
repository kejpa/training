<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use trainingAPI\Exceptions\AuthenticationException;

/**
 * Description of JwtHandler
 *
 * @author kjell
 */
abstract class JwtHandler {

    protected $secrect;
    protected $issuedAt;
    protected $expire;
    protected $issuer;

    final public function getToken(string $data): string {

        $token = array(
            //Adding the identifier to the token (who issue the token)
            "iss" => $this->issuer,
            // Adding the current timestamp to the token, for identifying that when the token was issued.
            "iat" => $this->issuedAt,
            // Token expiration
            "exp" => $this->expire,
            // Payload
            "data" => $data
        );

        return JWT::encode($token, $this->secrect, 'HS256');
    }

    final public function validate(string $token): bool {
        $decodedToken = JWT::decode($token, new Key($this->secrect, 'HS256'));

        if ($decodedToken->iss !== $this->issuer) {
            throw new AuthenticationException("Invalid issuer: {$decodedToken->iss}");
        } elseif ($decodedToken->exp < $this->issuedAt) {
            return false;
        }
        return true;
    }

    final public function getPayload($token): string {
        $decodedToken = JWT::decode($token, new Key($this->secrect, 'HS256'));

        return $decodedToken->data;
    }

    final public function getExpires(): int {
        return $this->expire;
    }
}
