<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of LoginController
 *
 * @author kjell
 */
final class LoginController {

    private $userRepository;
    private $loginHandler;

    public function __construct(UserRepository $userRepository, LoginHandler $loginHandler) {
        $this->userRepository = $userRepository;
        $this->loginHandler = $loginHandler;
    }

    public function getUserByToken(string $token): ?User {
        return $this->userRepository->getUserByToken($token);
    }

    public function logIn(Request $request): JsonResponse {
        $user = $this->loginHandler->handle(new Login(
                        (string) $request->request->get('username'),
                        (string) $request->request->get('password')
        ));

        if (!user) {
            $err = new stdClass();
            $err->message = ['Validation failed', "Unable to login"];
            return new JsonResponse($err, 405);
        }

        foreach ($user->getRecordedEvents() as $event) {
            if ($event instanceof UserWasLoggedIn) {
                $out = new stdClass();
                $out->user = $user;
                return new JsonResponse($out);
            }
        }

        $err = new stdClass();
        $err->message = ['Validation failed', "Unable to login"];
        return new JsonResponse($err, 405);
    }

    public function resetPassword(Request $request, array $param): JsonResponse {
        $request->query->add($param);
        // OBS!!!! Ta bort när en "riktig" webbserver används!!!
        // . i URL-parametrar fungerar inte i php:s inbyggda webbserver
        $user = str_replace("*", ".", $request->query->get('user')); 
        $username = filter_var($user, FILTER_VALIDATE_EMAIL);

        $user = $this->loginHandler->handle(new Login($username, ""));

        if (!user) {
            $err = new stdClass();
            $err->message = ['Validation failed', "User not found ($username)"];
            return new JsonResponse($err, 400);
        }

        $user->forgot();
        $this->userRepository->updateUser($user);

        $out = new stdClass();
        $out->user = $user;
        return new JsonResponse($out);
    }

}
