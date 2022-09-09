<?php

namespace trainingAPI\Login;

/**
 *
 * @author kjell
 */
interface LoginRepository {

    public function getUserByToken(string $token): ?User;
}
