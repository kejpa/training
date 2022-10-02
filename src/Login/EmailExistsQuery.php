<?php

namespace trainingAPI\Login;

/**
 *
 * @author kjell
 */
interface EmailExistsQuery {
    public function execute(string $email):bool;
}
