<?php

namespace trainingAPI\Login;

/**
 *
 * @author kjell
 */
interface UserRepository {

    public function getUserByToken(string $token): ?User;

    public function getUserByEmail(string $email): ?User;

    public function updateUser(User $user): int;

    public function addUser(User $user): int;
}
