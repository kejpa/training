<?php

declare (strict_types=1);

namespace trainingAPI\Login;

final class Login {

    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername():string {
        return $this->username;
    }

    public function getPassword():string {
        return $this->password;
    }

}
