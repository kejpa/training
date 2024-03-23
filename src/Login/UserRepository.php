<?php

namespace trainingAPI\Login;

use trainingAPI\Jwt\RefreshToken;

/**
 *
 * @author kjell
 */
interface UserRepository {

    public function getUserByRefreshToken(RefreshToken $token): ?User;

    public function getUserByEmail(string $email): ?User;

    public function updateUser(User $user): int;

    public function addUser(User $user): int;
}
