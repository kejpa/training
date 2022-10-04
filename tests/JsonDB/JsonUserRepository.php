<?php

declare (strict_types=1);

namespace tests\JsonDB;

use DateTimeImmutable;
use trainingAPI\Login\User;
use trainingAPI\Login\UserRepository;

/**
 * Description of JsonLoginRepository
 *
 * @author kjell
 */
final class JsonUserRepository implements UserRepository {

    private $db;

    public function __construct() {
        $json = file_get_contents(__DIR__ . "/users.json");
        $this->db = json_decode($json);
    }

    public function getUserByToken(string $token): ?User {
        foreach ($this->db as $value) {
            if ($value->token === $token) {
                return new User($value->id, $value->email, $value->firstname,
                        $value->lastname, $value->password, $value->token, new DateTimeImmutable($value->tokendate));
            }
        }

        return null;
    }

    public function getUserByEmail(string $email): ?User {
        foreach ($this->db as $rec) {
            if ($rec->email === $email) {
                return new User($rec->id, $rec->email, $rec->firstname,
                        $rec->lastname, $rec->password, $rec->token, new DateTimeImmutable($rec->tokendate));
            }
        }

        return null;
    }

    public function updateUser(User $user): int {
        $antal = 0;
        foreach ($this->db as $rec) {
            if ($rec->id === $user->getId()) {
                $rec = $user;
                $antal++;
            }
        }
        return $antal;
    }

    public function addUser(User $user): int {
        $max = 0;
        foreach ($this->db as $rec) {
            if ($rec->id > $max) {
                $max = $rec->id;
            }
        }
        $max++;
        $user->setId($max);
        $this->db[] = $user;

        return $max;
    }

}
