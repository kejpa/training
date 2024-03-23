<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

use JsonSerializable;
use stdClass;

/**
 * Description of RefreshToken
 *
 * @author kjell
 */
final class RefreshToken implements JsonSerializable {

    public function __construct(private int $id, private string $token, private int $expires = 0) {
        
    }

    public static function fromStdClass(stdClass $token) {
        return new RefreshToken($token->id, $token->token);
    }

    public function setExpires(int $expires) {
        $this->expires = $expires;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function getExpires(): int {
        return $this->expires;
    }

    public function jsonSerialize(): stdClass {
        $me = new stdClass();
        $me->id = $this->id;
        $me->token = $this->token;
        $me->expires = $this->expires;

        return $me;
    }
}
