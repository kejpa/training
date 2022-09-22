<?php

declare (strict_types=1);

namespace tests\JsonDB;

use trainingAPI\Login\LoginRepository;
use trainingAPI\Login\User;

/**
 * Description of JsonLoginRepository
 *
 * @author kjell
 */
final class JsonLoginRepository implements LoginRepository {

    private $db;

    public function __construct() {
        $json = file_get_contents(__DIR__ . "/users.json");
        $this->db = json_decode($json);
    }

    public function getUserByToken(string $token): ?User {
        foreach ($this->db as $value) {
            if ($value->token === $token) {
                return new User($value->id, $value->token, $value->firstname,
                        $value->lastname, $value->password, $value->token, new \DateTimeImmutable($value->tokendate));
            }
        }

        return null;
    }

}
