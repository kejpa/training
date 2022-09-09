<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Login\LoginController;

/**
 * Description of SessionController
 *
 * @author kjell
 */
final class SessionController {

    private $loginController;
    private $sessionRepository;

    public function __construct(LoginController $loginController, SessionRepository $sessionRepository) {
        $this->loginController = $loginController;
        $this->sessionRepository = $sessionRepository;
    }

    public function getAllSessions(Request $request): JsonResponse {
        $userToken = $request->headers->get("user-token") ?? "Abcd1234";
        $user = $this->loginController->getUserByToken($userToken);

        if (!$user || $user->getToken() !== $userToken) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 405);
        }
        
        $out=new stdClass();
        $out->sessions=$this->sessionRepository->getAllSessions($user->getId());
        
        return new JsonResponse($out, 200);
    }

}
