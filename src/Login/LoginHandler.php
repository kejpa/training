<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use trainingAPI\Exceptions\AuthenticationException;

/**
 * Description of LoginHandler
 *
 * @author kjell
 */
class LoginHandler {

    public function __construct(private UserRepository $userRepository) {
        
    }

    public function handle(Login $command): ?User {
        $user = $this->userRepository->getUserByEmail($command->getUsername());
        if ($user === null) {
            throw new AuthenticationException("Invalid username or password");
        }
        $user->logIn($command->getPassword());

        return $user;
    }
}
