<?php

declare (strict_types=1);

namespace trainingAPI\Login;

/**
 * Description of LoginHandler
 *
 * @author kjell
 */
final class LoginHandler {
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function handle(LogIn $command): ?User {
        $user = $this->userRepository->getUserByEmail($command->getUsername());
        if ($user === null) {
            return null;
        }
        $user->logIn($command->getPassword());

        return $user;
    }


}