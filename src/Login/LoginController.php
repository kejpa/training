<?php

declare (strict_types=1);

namespace trainingAPI\Login;

/**
 * Description of LoginController
 *
 * @author kjell
 */
final class LoginController {

    private $loginRepository;

    public function __construct(LoginRepository $loginRepository) {
        $this->loginRepository = $loginRepository;
    }

    public function getUserByToken(string $token): ?User {
        return $this->loginRepository->getUserByToken($token);
    }

}
