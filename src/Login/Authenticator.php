<?php

namespace trainingAPI\Login;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author kjell
 */
interface Authenticator {
    public function authenticate(Request $request): User;
}
