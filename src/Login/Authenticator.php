<?php

namespace trainingAPI\Login;

/**
 *
 * @author kjell
 */
interface Authenticator {
    public function authenticate(): User;
}
