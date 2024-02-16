<?php

declare (strict_types=1);

namespace trainingAPI\Login;

/**
 * Description of jsonAuthenticator
 *
 * @author kjell
 */
final class JsonAuthenticator implements Authenticator {

    public function authenticate(): User {
        return User::createFromRow([
                    "id" => 1,
                    "email" => "kjell@kejpa.com",
                    "firstname" => "Kjell",
                    "lastname" => "Hansen",
                    "password" => "$2y$10$8ww/G1jd8CrzAnqOaHKAIOXX2FH9SImqxc0lRETMR22NrL276RuUW",
                    "password_ohashat" => "pwd",
                    "token" => "Abcd1234",
                    "tokendate" => "2022-09-09 21:45:16",
                    "resettoken" => null,
                    "resetdate" => null
        ]);
    }
}
