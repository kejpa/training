<?php

namespace trainingAPI\Jwt;

/**
 *
 * @author kjell
 */
interface TokenRepository {

    public function addRefreshToken(RefreshToken $token): void;

    public function removeRefreshToken(RefreshToken $token): void;

    public function updateRefreshToken(RefreshToken $token): void;
}
